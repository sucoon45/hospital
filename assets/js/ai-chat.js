/**
 * Kamirex AI Medical Assistant - Logic
 */

const aiResponses = {
    "headache": "A headache can be caused by stress, dehydration, or tension. I recommend resting and staying hydrated. If it persists, please book an appointment with our **General Medicine** department.",
    "fever": "A fever often indicates an infection. Monitor your temperature. If it's above 39°C (102°F), visit our **Emergency** department immediately.",
    "stomach ache": "Stomach pain can range from indigestion to more serious issues. Avoid solid food for a few hours. I recommend seeing a **Gastroenterologist** if the pain is severe.",
    "cough": "A persistent cough might be a sign of a respiratory issue. I recommend our **Pulmonology** or **General Medicine** department.",
    "malaria": "Given the symptoms often seen in Nigeria, if you have fever, chills, and headache, it could be malaria. Please visit our **Laboratory** for a test and see a **General Practitioner**.",
    "emergency": "For immediate life-threatening situations, please call our 24/7 hotline: **+234 812 XXX XXXX** or visit the ER.",
}

function processSymptom(input) {
    const query = input.toLowerCase();
    let response = "I'm not sure about those symptoms. It is always safest to consult a professional. Would you like me to recommend a department or help you book an appointment?";
    
    for (const key in aiResponses) {
        if (query.includes(key)) {
            response = aiResponses[key];
            break;
        }
    }
    
    return response;
}

// UI Handling (Shared)
document.addEventListener('DOMContentLoaded', () => {
    const chatBtn = document.getElementById('aiChatBtn');
    const chatWindow = document.getElementById('aiChatWindow');
    const chatClose = document.getElementById('aiChatClose');
    const chatBody = document.getElementById('aiChatBody');
    const chatInput = document.getElementById('aiChatInput');
    const chatSend = document.getElementById('aiChatSend');

    if (!chatBtn) return;

    chatBtn.addEventListener('click', () => {
        chatWindow.classList.toggle('d-none');
    });

    chatClose.addEventListener('click', () => {
        chatWindow.classList.add('d-none');
    });

    function addMessage(msg, isUser = false) {
        const div = document.createElement('div');
        div.className = `mb-3 ${isUser ? 'text-end' : 'text-start'}`;
        div.innerHTML = `
            <div class="d-inline-block px-3 py-2 rounded-4 ${isUser ? 'bg-primary text-white' : 'bg-light text-dark'}" style="max-width: 80%;">
                ${msg}
            </div>
        `;
        chatBody.appendChild(div);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    chatSend.addEventListener('click', () => {
        const msg = chatInput.value.trim();
        if (msg) {
            addMessage(msg, true);
            chatInput.value = '';
            
            // AI Thinking Simulation
            setTimeout(() => {
                const reply = processSymptom(msg);
                addMessage(reply, false);
            }, 800);
        }
    });

    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') chatSend.click();
    });
});
