/* =========================================================
   SISTEM ABSENSI QR
   LOGIN JAVASCRIPT
   ========================================================= */


/* =========================================================
   DOM READY
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {

    initParticles();
    initRoleSwitcher();
    initPasswordToggle();
    initInputEffects();
    initLogin();

});


/* =========================================================
   PARTICLES
   ========================================================= */

function initParticles() {

    const canvas =
        document.getElementById("particleCanvas");

    if (!canvas) return;

    const ctx =
        canvas.getContext("2d");

    let particles = [];

    let width;
    let height;


    function resizeCanvas() {

        width =
            canvas.width =
            window.innerWidth;

        height =
            canvas.height =
            window.innerHeight;
    }


    resizeCanvas();

    window.addEventListener(
        "resize",
        resizeCanvas
    );


    function createParticles() {

        particles = [];

        const amount =
            Math.min(
                90,
                Math.floor(
                    window.innerWidth / 15
                )
            );

        for (
            let i = 0;
            i < amount;
            i++
        ) {

            particles.push({

                x:
                    Math.random() * width,

                y:
                    Math.random() * height,

                size:
                    Math.random() * 1.5
                    + 0.3,

                speedX:
                    (Math.random() - .5)
                    * .25,

                speedY:
                    (Math.random() - .5)
                    * .25,

                opacity:
                    Math.random() * .45
                    + .1

            });
        }
    }


    createParticles();


    function draw() {

        ctx.clearRect(
            0,
            0,
            width,
            height
        );


        particles.forEach(
            particle => {

                particle.x +=
                    particle.speedX;

                particle.y +=
                    particle.speedY;


                if (
                    particle.x < 0
                ) {
                    particle.x =
                        width;
                }

                if (
                    particle.x > width
                ) {
                    particle.x =
                        0;
                }


                if (
                    particle.y < 0
                ) {
                    particle.y =
                        height;
                }

                if (
                    particle.y > height
                ) {
                    particle.y =
                        0;
                }


                ctx.beginPath();

                ctx.arc(
                    particle.x,
                    particle.y,
                    particle.size,
                    0,
                    Math.PI * 2
                );

                ctx.fillStyle =
                    `rgba(
                        170,
                        170,
                        255,
                        ${particle.opacity}
                    )`;

                ctx.fill();

            }
        );


        requestAnimationFrame(draw);
    }


    draw();
}


/* =========================================================
   ROLE SWITCHER
   ========================================================= */

function initRoleSwitcher() {

    const tabs =
        document.querySelectorAll(
            ".role-tab"
        );

    const roleInput =
        document.getElementById("role");

    const identityLabel =
        document.getElementById(
            "identityLabel"
        );

    const identityInput =
        document.getElementById(
            "identity"
        );

    if (!tabs.length) return;


    tabs.forEach(tab => {

        tab.addEventListener(
            "click",
            () => {

                const role =
                    tab.dataset.role;


                tabs.forEach(
                    item => {

                        item.classList.remove(
                            "active"
                        );

                    }
                );


                tab.classList.add(
                    "active"
                );


                roleInput.value =
                    role;


                identityInput.value =
                    "";


                identityInput.classList.remove(
                    "input-changing"
                );


                void identityInput.offsetWidth;


                identityInput.classList.add(
                    "input-changing"
                );


                if (
                    role ===
                    "mahasiswa"
                ) {

                    identityLabel.textContent =
                        "NPM";

                    identityInput.placeholder =
                        "Masukkan NPM";

                }


                if (
                    role ===
                    "dosen"
                ) {

                    identityLabel.textContent =
                        "NIDN";

                    identityInput.placeholder =
                        "Masukkan NIDN";

                }


                if (
                    role ===
                    "admin"
                ) {

                    identityLabel.textContent =
                        "Username";

                    identityInput.placeholder =
                        "Masukkan username";

                }


                identityInput.focus();

            }
        );

    });
}


/* =========================================================
   PASSWORD TOGGLE
   ========================================================= */

function initPasswordToggle() {

    const password =
        document.getElementById(
            "password"
        );

    const toggle =
        document.getElementById(
            "togglePassword"
        );

    if (
        !password ||
        !toggle
    ) return;


    toggle.addEventListener(
        "click",
        () => {

            const isPassword =
                password.type ===
                "password";


            password.type =
                isPassword
                    ? "text"
                    : "password";


            toggle.textContent =
                isPassword
                    ? "◉"
                    : "○";


            toggle.setAttribute(
                "aria-label",
                isPassword
                    ? "Sembunyikan password"
                    : "Tampilkan password"
            );

        }
    );
}


/* =========================================================
   INPUT EFFECTS
   ========================================================= */

function initInputEffects() {

    const inputs =
        document.querySelectorAll(
            ".input-wrapper input"
        );


    inputs.forEach(input => {

        input.addEventListener(
            "focus",
            () => {

                input
                    .closest(".input-wrapper")
                    ?.classList.add(
                        "focused"
                    );

            }
        );


        input.addEventListener(
            "blur",
            () => {

                input
                    .closest(".input-wrapper")
                    ?.classList.remove(
                        "focused"
                    );

            }
        );

    });
}


/* =========================================================
   LOGIN
   ========================================================= */

function initLogin() {

    const form =
        document.getElementById(
            "loginForm"
        );

    const button =
        document.getElementById(
            "loginButton"
        );

    const message =
        document.getElementById(
            "loginMessage"
        );


    if (!form) return;


    form.addEventListener(
        "submit",
        async event => {

            event.preventDefault();


            const formData =
                new FormData(form);


            const identity =
                formData.get(
                    "identity"
                ).trim();

            const password =
                formData.get(
                    "password"
                ).trim();


            if (
                !identity ||
                !password
            ) {

                showMessage(
                    "NPM/NIDN/Username dan password wajib diisi.",
                    "error"
                );

                return;
            }


            button.classList.add(
                "loading"
            );


            hideMessage();


            try {

                const response =
                    await fetch(
                        "api/login.php",
                        {
                            method: "POST",
                            body: formData
                        }
                    );


                const result =
                    await response.json();


                if (
                    result.success
                ) {

                    showMessage(
                        "Login berhasil. Mengarahkan ke sistem...",
                        "success"
                    );


                    setTimeout(
                        () => {

                            window.location.href =
                                result.redirect;

                        },
                        700
                    );

                } else {

                    showMessage(
                        result.message ||
                        "Login gagal.",
                        "error"
                    );

                    button.classList.remove(
                        "loading"
                    );

                }

            } catch (error) {

                console.error(
                    error
                );


                showMessage(
                    "Server tidak dapat dihubungi. Periksa koneksi PHP dan database.",
                    "error"
                );


                button.classList.remove(
                    "loading"
                );

            }

        }
    );


    function showMessage(
        text,
        type
    ) {

        message.textContent =
            text;

        message.className =
            `login-message show ${type}`;
    }


    function hideMessage() {

        message.textContent =
            "";

        message.className =
            "login-message";
    }
}


/* =========================================================
   MOUSE PARALLAX
   ========================================================= */

document.addEventListener(
    "mousemove",
    event => {

        const card =
            document.querySelector(
                ".login-card"
            );

        if (!card) return;


        const x =
            (window.innerWidth / 2 -
                event.clientX) /
            80;

        const y =
            (window.innerHeight / 2 -
                event.clientY) /
            100;


        card.style.transform =
            `perspective(1000px)
             rotateY(${x * -0.15}deg)
             rotateX(${y * 0.15}deg)`;

    }
);


document.addEventListener(
    "mouseleave",
    () => {

        const card =
            document.querySelector(
                ".login-card"
            );

        if (!card) return;

        card.style.transform =
            "perspective(1000px) rotateY(0deg) rotateX(0deg)";
    }
);