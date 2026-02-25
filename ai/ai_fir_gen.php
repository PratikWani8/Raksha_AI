<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>AI FIR Generator</title>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<link rel="icon" href="../assets/favicon.jpg" type="image/x-icon" />

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');

body{
  font-family:'Plus Jakarta Sans',sans-serif;
  background:linear-gradient(135deg,#f4f6ff,#ffe6ec);
  margin:0;
  padding:20px;
}

.container{
  max-width:700px;
  margin:auto;
  background:#fff;
  padding:20px;
  border-radius:20px;
  box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

h2{
  text-align:center;
  color:#ff4d6d;
}

input,select,textarea{
  width:95%;
  margin:10px 0;
  padding:12px;
  border-radius:10px;
  border:1px solid #ddd;
}

textarea{
  height:180px;
  resize:none;
}

button{
  padding:12px;
  border:none;
  border-radius:10px;
  background:#ff4d6d;
  color:#fff;
  font-weight:600;
  cursor:pointer;
  width:100%;
  margin-top:10px;
}

.desc-box{
  position:relative;
}

.mic-btn{
  position:absolute;
  bottom:15px;
  right:15px;
  background:#ff4d6d;
  width:45px;
  height:45px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  cursor:pointer;
}

.mic-btn.recording{
  background:red;
}
</style>
</head>

<body>
<div class="container">
<h2>🧾 A.I FIR Generator</h2>

<input id="name" placeholder="Your Name" readonly>
<input id="phone" placeholder="Phone Number" readonly>
<input id="location" placeholder="Location" readonly>



<div class="desc-box">
<textarea id="description" placeholder="Describe incident or use voice..."></textarea>

<div class="mic-btn" onclick="startVoice()">
<svg width="20" height="20" fill="#fff" viewBox="0 0 24 24">
<path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 1 0-6 0v6a3 3 0 0 0 3 3z"/>
<path d="M19 11a7 7 0 0 1-14 0H3a9 9 0 0 0 18 0h-2z"/>
<path d="M12 19v3"/>
</svg>
</div>
</div>

<select id="lang">
<option value="en">English</option>
<option value="hi">Hindi</option>
<option value="mr">Marathi</option>
</select>

<button onclick="generateFIR()">Generate FIR (AI)</button>
<button onclick="downloadPDF()">Download PDF</button>

<textarea id="output"></textarea>
</div>

<script>

// -------- FETCH USER DATA --------
async function loadUser(){
    try{
        let res = await fetch("get_user.php");
        let data = await res.json();

        if(data.name){
            document.getElementById("name").value = data.name;
        }

        if(data.phone){
            document.getElementById("phone").value = data.phone;
        }

    }catch(err){
        console.log(err);
    }
}
loadUser();

// -------- VOICE INPUT --------
let recognition;
function startVoice(){
  if(!('webkitSpeechRecognition' in window)){
    alert("Voice not supported");
    return;
  }

  recognition = new webkitSpeechRecognition();

  let lang = document.getElementById("lang").value;

  if(lang === "hi") recognition.lang = "hi-IN";
  else if(lang === "mr") recognition.lang = "mr-IN";
  else recognition.lang = "en-IN";

  let mic = document.querySelector('.mic-btn');
  mic.classList.add('recording');

  recognition.start();

  recognition.onresult = function(e){
    document.getElementById('description').value += e.results[0][0].transcript + " ";
  }

  recognition.onend = ()=> mic.classList.remove('recording');
}

// -------- NLP MODEL --------
let model;

// Vocabulary (expanded)
const vocabulary = [
  "follow","stalk","attack","hit","touch","harass",
  "steal","kidnap","threat","abuse","rob","snatch",
  "molest","rape","force","grab","beat","danger",
  "hack","otp","fraud","scam","cyber","online","morph",
  "phishing","password","bank","account","money","transaction"
];

// Training dataset
const trainingData = [
  {text:"he was following me stalking", label:1},
  {text:"someone attacked me and hit me", label:2},
  {text:"he touched me wrongly harassment", label:0},
  {text:"my phone was stolen theft", label:3},
  {text:"someone tried to kidnap me", label:4},
  {text:"he abused and threatened me", label:0},
  {text:"they tried to snatch my bag", label:3},
  {text:"he was continuously following me", label:1},
  {text:"he beat me badly", label:2},
  {text:"man tried to grab me kidnap", label:4},
  {text:"someone hacked my account", label:5},
  {text:"i got otp fraud message", label:5},
  {text:"online scam money deducted", label:5},
  {text:"phishing link stole my password", label:5},
  {text:"bank fraud transaction happened", label:5},
  {text:"someone morph my photo", label:5},
  {text:"someone morph my video", label:5}
];

function textToVector(text){
    text = text.toLowerCase();
    return vocabulary.map(word => text.includes(word) ? 1 : 0);
}

function labelToOneHot(label){
    let arr = [0,0,0,0,0,0];
    arr[label] = 1;
    return arr;
}

function prepareData(){
    const xs = trainingData.map(d => textToVector(d.text));
    const ys = trainingData.map(d => labelToOneHot(d.label));

    return {
        xs: tf.tensor2d(xs),
        ys: tf.tensor2d(ys)
    };
}

// Load & train model
async function loadModel(){

    const data = prepareData();

    model = tf.sequential();

    model.add(tf.layers.dense({
        units:32,
        inputShape:[vocabulary.length],
        activation:'relu'
    }));

    model.add(tf.layers.dense({
        units:16,
        activation:'relu'
    }));

    model.add(tf.layers.dense({
        units:6,
        activation:'softmax'
    }));

    model.compile({
        loss:'categoricalCrossentropy',
        optimizer:'adam',
        metrics:['accuracy']
    });

    await model.fit(data.xs, data.ys, {
        epochs:100
    });

    console.log("✅ NLP Model Trained");
}

loadModel();

// -------- PREDICTION --------
function predictType(text){

    const input = tf.tensor2d([textToVector(text)]);
    const prediction = model.predict(input);

    const index = prediction.argMax(1).dataSync()[0];

    const types = ["Harassment","Stalking","Assault","Theft","Kidnapping","Cybercrime"];

    return types[index];
}

// -------- IPC SECTION MAPPING --------
function getIPCSections(type, text){

    text = text.toLowerCase();

    if(type === "Harassment"){
        if(text.includes("sexual") || text.includes("touch")){
            return "IPC 354 (Outraging modesty), IPC 509 (Insulting modesty)";
        }
        return "IPC 509 (Insulting modesty)";
    }

    if(type === "Stalking"){
        return "IPC 354D (Stalking)";
    }

    if(type === "Assault"){
        return "IPC 351 (Assault), IPC 352 (Punishment for assault)";
    }

    if(type === "Theft"){
        return "IPC 379 (Theft)";
    }

    if(type === "Kidnapping"){
        return "IPC 363 (Kidnapping)";
    }

    if(type === "Cybercrime"){
        return "IT Act 66 (Computer related offence), IT Act 66C (Identity theft), IT Act 66D (Cheating by impersonation)";
    }

    return "Applicable sections to be determined";
}

function generateFIR(){

    let name = document.getElementById("name").value;
    let phone = document.getElementById("phone").value;
    let location = document.getElementById("location").value;
    let description = document.getElementById("description").value;
    let lang = document.getElementById("lang").value;

    if(!description.trim()){
        alert("Please describe the incident");
        return;
    }

    let type = predictType(description);
    let ipc = getIPCSections(type, description);

    let now = new Date();
    let date = now.toLocaleDateString();
    let time = now.toLocaleTimeString();

    let firNo = "FIR-" + now.getTime();

    let fir = "";

    // -------- ENGLISH FIR --------
    if(lang === "en"){
        fir = `
==================================================
           FIRST INFORMATION REPORT (FIR)
==================================================

FIR No: ${firNo}
Date: ${date}
Time: ${time}

--------------------------------------------------
COMPLAINANT DETAILS
--------------------------------------------------
Name            : ${name}
Contact Number  : ${phone}
Address         : ${location}

--------------------------------------------------
INCIDENT DETAILS
--------------------------------------------------
Type of Offence : ${type}
Applicable Law  : ${ipc}
Date & Time     : ${date}, ${time}
Place           : ${location}

--------------------------------------------------
DESCRIPTION OF INCIDENT
--------------------------------------------------
${description}

--------------------------------------------------
DECLARATION
--------------------------------------------------
I hereby declare that the information provided above is true
to the best of my knowledge and belief. I request the police
authorities to take appropriate legal action.

Signature: _______________________

Name: ${name}
Date: ${date}
Place: ${location}

==================================================
    `;
    }

    // -------- HINDI FIR --------
    if(lang === "hi"){
        fir = `
==================================================
          प्रथम सूचना रिपोर्ट (FIR)
==================================================

FIR नंबर: ${firNo}
दिनांक: ${date}
समय: ${time}

--------------------------------------------------
शिकायतकर्ता विवरण
--------------------------------------------------
नाम: ${name}
मोबाइल: ${phone}
पता: ${location}

--------------------------------------------------
घटना विवरण
--------------------------------------------------
अपराध का प्रकार: ${type}
लागू कानून: ${ipc}
स्थान: ${location}

--------------------------------------------------
घटना का विवरण
--------------------------------------------------
${description}

--------------------------------------------------
घोषणा
--------------------------------------------------
मैं घोषणा करता/करती हूँ कि दी गई जानकारी सत्य है।

हस्ताक्षर: ____________________

नाम: ${name}
दिनांक: ${date}

==================================================
        `;
    }

    // -------- MARATHI FIR --------
    if(lang === "mr"){
        fir = `
==================================================
        प्रथम माहिती अहवाल (FIR)
==================================================

FIR क्रमांक: ${firNo}
दिनांक: ${date}
वेळ: ${time}

--------------------------------------------------
तक्रारदार माहिती
--------------------------------------------------
नाव: ${name}
मोबाईल: ${phone}
पत्ता: ${location}

--------------------------------------------------
घटना तपशील
--------------------------------------------------
गुन्ह्याचा प्रकार: ${type}
कायदा: ${ipc}
ठिकाण: ${location}

--------------------------------------------------
घटनेचे वर्णन
--------------------------------------------------
${description}

--------------------------------------------------
घोषणा
--------------------------------------------------
मी दिलेली माहिती खरी आहे.

स्वाक्षरी: ____________________

नाव: ${name}
दिनांक: ${date}

==================================================
        `;
    }

    document.getElementById("output").value = fir;
}

// -------- PDF --------
async function downloadPDF(){
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    let text = document.getElementById("output").value;
    let lines = doc.splitTextToSize(text, 180);

    doc.text(lines,10,10);
    doc.save("FIR.pdf");
}

// -------- LOCATION --------
async function getAddress(lat, lon){
  try{
    let res = await fetch(`get_address.php?lat=${lat}&lon=${lon}`);
    let data = await res.json();

    return data.display_name;
  }catch(err){
    console.log(err);
    return null;
  }
}

navigator.geolocation.getCurrentPosition(async pos=>{
  let lat = pos.coords.latitude;
  let lon = pos.coords.longitude;

  let address = await getAddress(lat, lon);

  if(address){
    document.getElementById("location").value = address;
  } else {
    document.getElementById("location").value = lat + ", " + lon;
  }
}, err=>{
  alert("Location access denied!");
});


</script>
</body>
</html>