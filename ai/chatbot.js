// TIPS 
const tips = [
    "Always share your live location with a trusted contact when traveling late.",
    "Keep your phone in your hand in isolated areas.",
    "Move to crowded places if you feel unsafe.",
    "Trust your instincts.",
    "Use Raksha AI to avoid dangerous areas."
];
 
function toggleChat() {
    const chat = document.getElementById('chat-container');
    chat.style.display = (chat.style.display === 'flex') ? 'none' : 'flex';
}

function speak(text) {
    const u = new SpeechSynthesisUtterance(text);
    speechSynthesis.speak(u);
}

function addMessage(text, type) {
    const box = document.getElementById('chat-box');
    const div = document.createElement('div');
    div.className = "msg " + type + "-msg";
    div.innerText = text;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;

    if (type === 'bot') speak(text);
}

// voice input
function startVoiceInput() {
    const micBtn = document.querySelector('.mic-btn');

    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();

    recognition.lang = "en-IN";
    recognition.interimResults = false;

    micBtn.classList.add("listening");

    recognition.start();

    recognition.onresult = function(event) {
        const text = event.results[0][0].transcript;
        document.getElementById("user-input").value = text;
        micBtn.classList.remove("listening");
        sendMessage();
    };

    recognition.onerror = function(event) {
    console.log("Speech Error:", event.error);

    let msg = "❌ Voice recognition failed.";

    if (event.error === "not-allowed") {
        msg = "❌ Microphone permission denied.";
    }
    else if (event.error === "no-speech") {
        msg = "❌ No speech detected. Try again.";
    }
    else if (event.error === "audio-capture") {
        msg = "❌ No microphone found.";
    }
    else if (event.error === "network") {
        msg = "❌ Network error.";
    }

    addMessage(msg, "bot");
};

    recognition.onend = function() {
        micBtn.classList.remove("listening");
    };
}

// SOS 
async function handleSOS() {
    addMessage("🚨 Initiating Emergency Protocol. Accessing GPS...", "bot");
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            const locationString = `Lat: ${lat}, Lng: ${lng}`;
            
            const formData = new FormData();
            formData.append('send', 'true');
            formData.append('location', locationString);
            formData.append('msg', 'SOS triggered via Raksha AI Chatbot');

            try {
                const response = await fetch('send_sos.php', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    addMessage("✅ SOS Alert sent to authorities! Help is being dispatched to your location.", "bot");
                } else {
                    addMessage("⚠️ Alert logged locally, but server communication failed.", "bot");
                }
            } catch (error) {
                addMessage("❌ Connection error. Please use the physical SOS button!", "bot");
            }

        }, (error) => {
            addMessage("❌ Location access denied. I cannot send an accurate SOS without GPS.", "bot");
        });
    } else {
        addMessage("❌ Geolocation is not supported by this browser.", "bot");
    }
}

// Find Nearby Police within 10 KM 
function findNearbyPolice() {
    addMessage("📍 Finding nearby police stations within 10 km...", "bot");

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            const radius = 10000;

            try {
                const query = `
                [out:json];
                node["amenity"="police"](around:${radius},${lat},${lng});
                out;
                `;

                const response = await fetch("https://overpass-api.de/api/interpreter", {
                    method: "POST",
                    body: query
                });

                const data = await response.json();

                if (!data.elements || data.elements.length === 0) {
                    addMessage("❌ No police stations found within 10 km.", "bot");
                    return;
                }

                function getDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371; 
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;

                    const a =
                        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) *
                        Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) *
                        Math.sin(dLon / 2);

                    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                }

                const stations = data.elements.map(p => {
                    const distance = getDistance(lat, lng, p.lat, p.lon);
                    return {
                        name: p.tags?.name || "Police Station",
                        lat: p.lat,
                        lon: p.lon,
                        distance: distance
                    };
                }).sort((a, b) => a.distance - b.distance);

                let message = "🚔 Nearest Police Stations (within 10 km):\n";

                stations.slice(0, 3).forEach((s, i) => {
                    message += `${i + 1}. ${s.name} (${s.distance.toFixed(2)} km)\n`;
                });

                addMessage(message, "bot");

                const nearest = stations[0];
                const url = `https://www.google.com/maps/dir/?api=1&destination=${nearest.lat},${nearest.lon}`;
                window.open(url, "_blank");

                speak("Showing nearest police station.");

            } catch (error) {
                addMessage("❌ Error fetching police stations.", "bot");
            }

        }, () => {
            addMessage("❌ Location access denied.", "bot");
        });
    } else {
        addMessage("❌ Geolocation not supported.", "bot");
    }
}

//  AI MODEL 
let model;

async function loadModel() {
    try {
        model = await tf.loadLayersModel('model/model.json');
        console.log("AI Model Loaded");
    } catch {
        console.log("No AI model found, using fallback.");
    }
}
loadModel();

async function predictRisk(lat,lng){
    if(!model) return 0.3;

    const input = tf.tensor2d([[lat/90,lng/180]]);
    const pred = model.predict(input);
    return pred.dataSync()[0];
}

//  SAFETY 
async function checkSafetyAI() {
    addMessage("📍 Checking safety...", "bot");

    navigator.geolocation.getCurrentPosition(async (pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;

        let risk = await predictRisk(lat,lng);

        const hour = new Date().getHours();

        if(hour >= 22 || hour <= 5) risk += 0.2;

        risk = Math.min(1, risk);

        let msg;

        if (risk > 0.7) {
            msg = "🚨 High Risk! You are not safe.";
            speak("Danger zone detected");
        }
        else if (risk > 0.4) {
            msg = "⚠️ Medium Risk. Stay alert.";
        }
        else {
            msg = "✅ You are in a safe area.";
        }

        addMessage(msg,"bot");
    });
}

//  Emotion Detection
function detectDangerEmotion(text) {
    const dangerWords = [
        "scared",
        "unsafe",
        "fear",
        "help me",
        "someone following",
        "follow",
        "danger",
        "threat",
        "panic"
    ];

    return dangerWords.some(word => text.includes(word));
}

// Find Nearby Safe Places
function findSafePlaces() {
    addMessage("📍 Finding nearby safe places (police, hospitals, public areas)...", "bot");

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(async (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            const radius = 10000; 

            try {
                const query = `
                [out:json];
                (
                    node["amenity"="police"](around:${radius},${lat},${lng});
                    node["amenity"="hospital"](around:${radius},${lat},${lng});
                    node["amenity"="cafe"](around:${radius},${lat},${lng});
                );
                out;
                `;

                const response = await fetch("https://overpass-api.de/api/interpreter", {
                    method: "POST",
                    body: query
                });

                const data = await response.json();

                if (!data.elements || data.elements.length === 0) {
                    addMessage("❌ No safe places found nearby.", "bot");
                    return;
                }

                function getDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;

                    const a =
                        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) *
                        Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) *
                        Math.sin(dLon / 2);

                    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                }

                const places = data.elements.map(p => ({
                    name: p.tags?.name || p.tags?.amenity,
                    type: p.tags?.amenity,
                    lat: p.lat,
                    lon: p.lon,
                    distance: getDistance(lat, lng, p.lat, p.lon)
                })).sort((a, b) => a.distance - b.distance);

                let message = "🛡️ Nearby Safe Places:\n";

                places.slice(0, 5).forEach((p, i) => {
                    message += `${i + 1}. ${p.name} (${p.type}) - ${p.distance.toFixed(2)} km\n`;
                });

                addMessage(message, "bot");

                const nearest = places[0];
                const url = `https://www.google.com/maps/dir/?api=1&destination=${nearest.lat},${nearest.lon}`;
                window.open(url, "_blank");

                speak("Showing nearest safe place.");

            } catch (error) {
                addMessage("❌ Error fetching safe places.", "bot");
            }

        }, () => {
            addMessage("❌ Location access denied.", "bot");
        });
    }
}

//  Siren Sound Setup
let sirenAudio = new Audio("https://actions.google.com/sounds/v1/alarms/alarm_clock.ogg");
sirenAudio.loop = true;
sirenAudio.volume = 1.0;

//  Vibration Control
let vibrationInterval = null;

function startVibrationLoop() {
    if (!navigator.vibrate) return;

    if (vibrationInterval) return; 

    vibrationInterval = setInterval(() => {
        navigator.vibrate([500, 300, 500]); 
    }, 1300); 
}

function stopVibrationLoop() {
    if (vibrationInterval) {
        clearInterval(vibrationInterval);
        vibrationInterval = null;
    }

    navigator.vibrate(0); 
}
function startSiren() {
    sirenAudio.play().catch(() => {
        console.log("Audio play blocked until user interaction");
    });
   startVibrationLoop();
    addMessage("🚨 Siren activated!", "bot");
}

function stopSiren() {
    sirenAudio.pause();
    sirenAudio.currentTime = 0;
    stopVibrationLoop();
    addMessage("✅ Siren stopped.", "bot");
}

// send message function
function sendMessage() {
    const input = document.getElementById('user-input');
    const text = input.value.toLowerCase().trim();

    if (!text) return;

    addMessage(input.value, "user");
    input.value = "";

    setTimeout(() => {

        if (text.includes("police")) {
            findNearbyPolice();
        }
        else if (text.includes("sos") || text.includes("help")) {
            handleSOS();
        }
        else if (text.includes("am i safe")) {
            checkSafetyAI();
        }
        else if (text.includes("tip")) {
            const t = tips[Math.floor(Math.random()*tips.length)];
            addMessage("Tip: "+t,"bot");
        }
        else if (detectDangerEmotion(text)) {
        addMessage(input.value, "user");
        input.value = "";
        addMessage("⚠️ You seem unsafe. Do you want me to send SOS or find safe places nearby?", "bot");
        speak("You seem unsafe. Help is available.");
        return;
    }
        else if (text.includes("safe place") || text.includes("safe area") || text.includes("nearby safe")) {
        addMessage(input.value, "user");
        input.value = "";
        findSafePlaces();
        return;
    }
    else if (text.includes("siren") || text.includes("danger") || text.includes("alarm")) {
        addMessage(input.value, "user");
        input.value = "";
        startSiren();
        return;
    }
    else if (text.includes("stop") || text.includes("stop alarm")) {
        addMessage(input.value, "user");
        input.value = "";
        stopSiren();
        return;
    }
    else {
            addMessage("Type 'SOS', 'Nearby Police', 'Am I safe', or 'Tips'.","bot");
        }

    }, 500);
}