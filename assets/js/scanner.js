window.QRScannerUI={
 async startCamera(elementId,onScan){if(!window.Html5Qrcode)throw new Error('Library scanner belum termuat.');const scanner=new Html5Qrcode(elementId);await scanner.start({facingMode:'environment'},{fps:10,qrbox:{width:250,height:250}},txt=>{onScan(txt);scanner.stop().catch(()=>{});},()=>{});return scanner},
 async scanFile(input,onScan){if(!window.Html5Qrcode)throw new Error('Library scanner belum termuat.');const scanner=new Html5Qrcode('qr-reader');try{const txt=await scanner.scanFile(input,true);onScan(txt);}finally{scanner.clear().catch(()=>{});}}
};
