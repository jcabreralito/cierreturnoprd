<style>
        /* Estilos para el chat flotante */
        /* .relative {
            position: relative;
        } */
        .chat-container {

            position: fixed;
            bottom: 5px;
            right: 70px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .chat-toggle-btn {
            width: 3rem;
            height: 3rem;
            //background-color: #0d6efd;
            /* Cambiado a azul primario de Bootstrap */
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
            margin-bottom: 10px;
            background-color: #25D366;
        }

        .chat-box {
            display: none;
            width: 500px;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .chat-box.active {
            display: block;
        }

        .chat-header {
            background: #0d6efd;
            border-radius: 15px 15px 0 0;
            color: white;
            padding: 10px 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .chat-header i {
            cursor: pointer;
        }

        .chat-body {
            height: 300px;
            overflow-y: auto;
            padding: 10px;
        }

        .chat-footer {
            padding: 10px;
            border-top: 1px solid #eee;
        }

        .input-group {
            display: flex;
            gap: 1px;
            align-items: flex-end;
            /* Alinea los elementos al final */
        }

        .chat-message {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            /* Alinea los elementos al inicio */
            margin-bottom: 16px;
            /* Espacio entre mensajes */
        }

        .user-message {
            flex-direction: row-reverse;
        }


        .message-content {
            max-width: 80%;
            padding: 12px 15px;
            border-radius: 15px;
            font-size: 12px;
            margin-top: 4px;
            white-space: pre-wrap;
            /* Preserva los saltos de línea */
            word-wrap: break-word;
            /* Permite que las palabras largas se rompan */
        }

        .user-message .message-content {
            background-color: #f0f1f1f6;
            color: black;
            margin-right: 10px;
        }

        .bot-message .message-content {
            background-color: #f8f9fa;
            margin-left: 10px;
            border: 1px solid #e9ecef;
        }

        .avatar {
            width: 30px;
            height: 30px;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2px;
            /* Espacio entre avatar y nombre */
        }

        .avatar-name {
            font-size: 10px;
            color: #666;
            text-align: center;
            width: 100%;
            line-height: 1.2;
            /* Mejor espaciado para el nombre */
        }

        .avatar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            min-width: 40px;
            /* Ancho fijo para el contenedor del avatar */
        }





        .message-input {
            flex-grow: 1;
            border-radius: 12px;
            border: 1px solid #F0F0F0;
            padding: 8px 12px;
            font-size: 12px;
            resize: none;
            height: 40px;
            /* Mismo alto que el botón */
            min-height: 40px;
            max-height: 40px;
            line-height: 1.5;
            overflow-y: auto;
        }

        .message-input:focus {
            outline: none;
            border-color: #0d6efd;
        }

        .user-message .avatar {
            background-color: #007bff;
        }

        .user-message .avatar {
            background-color: #0d6efd;
        }


        .send-btn {
            width: 40px;
            height: 40px;
            min-height: 40px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            border: none;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            padding: 0;
        }


        .send-btn:hover {
            background-color: #0b5ed7;
        }

        .send-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }


        .user-message .avatar {
            background-color: #007bff;
        }

        .bot-message .avatar {
            background-color: #0d6efd;
        }

        .avatar-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .avatar-name {
            font-size: 10px;
            color: #666;
            text-align: center;
            width: 100%;
        }

        .user-message .avatar-container {
            margin-left: 8px;
        }

        .bot-message .avatar-container {
            margin-right: 8px;
        }


        .typing-indicator {
            padding: 8px 15px;
            margin: 0;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 15px;
            font-size: 12px;
            min-width: 50px;
            /* Ancho mínimo para los puntos */
        }

        .typing-animation {
            display: flex;
            gap: 4px;
            justify-content: center;
            /* Centra los puntos horizontalmente */
            padding: 2px 0;
            /* Espaciado vertical */
        }

        .typing-dot {
            width: 6px;
            /* Puntos más pequeños */
            height: 6px;
            /* Puntos más pequeños */
            background: #0d6efd;
            border-radius: 50%;
            opacity: 0.6;
            animation: typing 1s infinite ease-in-out;
        }

        /* Ajuste de la animación para un movimiento más sutil */
        @keyframes typing {
            0% {
                transform: translateY(0px);
                opacity: 0.6;
            }

            50% {
                transform: translateY(-6px);
                /* Reducido el desplazamiento vertical */
                opacity: 1;
            }

            100% {
                transform: translateY(0px);
                opacity: 0.6;
            }
        }
    </style>




    <div class="chat-container">
        <div class="chat-toggle-btn" id="chatToggleBtn">
        {{--  <svg  viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
  <!-- Fondo circular verde WhatsApp -->
  <circle cx="50" cy="50" r="48" fill="#25D366" stroke="white" stroke-width="4" />

  <!-- Signo de interrogación en el centro -->
  <text x="50%" y="55%" text-anchor="middle" dominant-baseline="middle" font-size="55" fill="white" font-family="Arial, sans-serif">?</text>
</svg>  --}}

<i class="fa-solid fa-question"></i>

            <!-- <i class="fas fa-comment"></i> -->
        </div>

        <div class="chat-box" id="chatBox">
            <div class="chat-header">

                <span id="asistenteTitulo" class="text-center">Asistente IA

                </span>
                <i class="fas fa-times" id="closeChat"></i>

            </div>

            <div class="chat-body" id="chatBody">


            </div>

            <div class="chat-footer">
                <div class="input-group">
                    <textarea id="messageInput" class="message-input" rows="2"
                        placeholder="Escribe tu mensaje aquí..."></textarea>
                    <button id="sendBtn" class="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
                <div class="mt-2" style="font-size: 10px; color: #666;">
                    <strong>Nota:</strong> Este asistente utiliza IA para responder preguntas. Las respuestas generadas por IA pueden no ser precisas o confiables. Por favor, verifica la información antes de tomar decisiones basadas en ella.
</div>
            </div>
        </div>




    </div>
    <div style="display:none">
        <div id="username">{{auth()->user()->Login}}</div>
        <div id="personal">{{auth()->user()->Personal}}</div>
        <div id="id_usuario">{{auth()->user()->Id_Usuario}}</div>
        <div id="asistantId">{{$assistantId}}</div>
    </div>





    <script>

        const personal = document.getElementById('personal').textContent;

        const username = document.getElementById('username').textContent;
        const id_usuario = document.getElementById('id_usuario').textContent;

        const urlUser = `https://servicios.litoprocess.com/colaboradores/api/foto/${personal}`;
        const baseAPIUrl = 'https://servicios.litoprocess.com/openai';
        const asistantId=document.getElementById('asistantId').textContent;
        document.addEventListener('DOMContentLoaded', function () {
            const chatToggleBtn = document.getElementById('chatToggleBtn');
            const chatBox = document.getElementById('chatBox');
            const closeChat = document.getElementById('closeChat');
            const sendBtn = document.getElementById('sendBtn');
            const messageInput = document.getElementById('messageInput');
            const chatBody = document.getElementById('chatBody');

            // Abrir/cerrar el chat
            chatToggleBtn.addEventListener('click', function () {
                chatBox.classList.toggle('active');
            });

            closeChat.addEventListener('click', function () {
                chatBox.classList.remove('active');
            });

            // Enviar mensaje al hacer clic en el botón
            sendBtn.addEventListener('click', function () {
                sendMessage();
            });

            // Enviar mensaje al presionar Enter (sin Shift)
            messageInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            async function sendMessage() {
                const message = messageInput.value.trim();
                if (!message) return;

                // Deshabilitar interfaz
                disableInterface();

                // Añadir mensaje del usuario al chat
                addUserMessage(message);

                // Limpiar el campo de entrada
                messageInput.value = '';

                try {
                    await fetchAssistantResponse(message);
                } catch (error) {
                    console.error('Error:', error);
                    addBotMessage('Lo siento, ha ocurrido un error al procesar tu solicitud.');
                } finally {
                    hideTypingIndicator();
                    enableInterface();
                }
            }

            function addUserMessage(text) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'chat-message user-message';
                messageDiv.innerHTML = `
        <div class="avatar-container">
            <div class="avatar">
               <img src="${urlUser}"
                     alt="Usuario"
                     style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">
            </div>
            <span class="avatar-name">${username}</span>
        </div>
        <div class="message-content">${text}</div>
    `;
                chatBody.appendChild(messageDiv);
                scrollToBottom();
            }


            function addBotMessage(text) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'chat-message bot-message';
                messageDiv.innerHTML = `
        <div class="avatar-container">
            <div class="avatar">
                <i class="fas fa-robot"></i>
            </div>
            <span class="avatar-name">Asistente</span>
        </div>
        <div class="message-content">${text}</div>
    `;
                chatBody.appendChild(messageDiv);
                scrollToBottom();
            }

            function showTypingIndicator() {
                const typingDiv = document.createElement('div');
                typingDiv.className = 'chat-message bot-message';
                typingDiv.id = 'typingIndicator';
                typingDiv.innerHTML = `
        <div class="avatar-container">
            <div class="avatar">
                <i class="fas fa-brain"></i>
            </div>
            <span class="avatar-name">Asistente</span>
        </div>
        <div class="typing-indicator">
            <div class="typing-animation">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>
    `;
                chatBody.appendChild(typingDiv);
                scrollToBottom();
            }

            function disableInterface() {
                messageInput.disabled = true;
                messageInput.classList.add('input-disabled');
                sendBtn.disabled = true;
                messageInput.placeholder = 'Espera mientras proceso tu pregunta...';
            }

            function enableInterface() {
                messageInput.disabled = false;
                messageInput.classList.remove('input-disabled');
                sendBtn.disabled = false;
                messageInput.placeholder = 'Escribe tu mensaje aquí...';
            }

            function hideTypingIndicator() {
                const typingIndicator = document.getElementById('typingIndicator');
                if (typingIndicator) {
                    typingIndicator.remove();
                }
            }

            function scrollToBottom() {
                chatBody.scrollTop = chatBody.scrollHeight;
            }

            async function fetchAssistantResponse(question) {
                try {
                    showTypingIndicator();
                    const response = await fetch(`${baseAPIUrl}/sam-asistant/user-question-stream`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            userId: id_usuario,
                            IdAsistant:asistantId,
                            question
                        }),
                    });

                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }

                    // Crear el mensaje del bot una sola vez
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'chat-message bot-message';
                    messageDiv.innerHTML = `
    <div class="avatar-container">
        <div class="avatar">
            <i class="fas fa-robot"></i>
        </div>
        <span class="avatar-name">Asistente</span>
    </div>
    <div class="message-content"></div>
`;
                    hideTypingIndicator();
                    chatBody.appendChild(messageDiv);
                    const messageContent = messageDiv.querySelector('.message-content');
                    let accumulatedText = '';

                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();

                    while (true) {
                        const { value, done } = await reader.read();
                        if (done) break;

                        const chunk = decoder.decode(value);
                        const lines = chunk.split('\n\n');

                        for (const line of lines) {
                            if (line.startsWith('data: ')) {
                                try {
                                    const data = JSON.parse(line.substring(6));
                                    if (data.data) {
                                        // Reemplazar \n con <br> o preservar los saltos de línea existentes
                                        accumulatedText += data.data;
                                        messageContent.innerHTML = accumulatedText
                                            .replace(/\n/g, '<br>')
                                            .replace(/\r/g, '');
                                        scrollToBottom();
                                    }
                                } catch (e) {
                                    console.error('Error al parsear datos:', e);
                                }
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error al obtener respuesta:', error);
                    hideTypingIndicator();
                    addBotMessage('Lo siento, ha ocurrido un error al procesar tu solicitud.');
                } finally {
                    enableInterface();
                }
            }

            async function init() {
                const response = await fetch(`${baseAPIUrl}/sam-asistant/get-messages/${id_usuario}/asistant/${asistantId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });
                if (!response.ok) {
                    document.querySelector('.chat-toggle-btn').style.display = 'none';
                    document.querySelector('#asistenteTitulo').textContent = 'Asistente NO DISPONIBLE';
                    disableInterface();
                    throw new Error('Error en la respuesta del servidor');
                }
                const data = await response.json();
                const messages = data.messages;
                addBotMessage("Hola, ¿en qué puedo ayudarte hoy?");
                if (messages.length === 0) {
                    return;
                }
                for (const message of messages) {
                    if (message.role === "user") {
                        addUserMessage(message.text);
                    } else {
                        addBotMessage(message.text);
                    }
                }

            }
            init();
        });
    </script>
