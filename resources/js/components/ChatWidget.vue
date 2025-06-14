<!-- ChatWidget.vue -->
<template>
    <div class="chat-widget">
        <v-dialog v-model="isOpen" width="400" persistent>
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                    fab
                    large
                    color="primary"
                    class="chat-widget-button"
                    v-bind="attrs"
                    v-on="on"
                >
                    <v-icon>mdi-chat</v-icon>
                </v-btn>
            </template>

            <v-card class="chat-container">
                <v-card-title class="chat-header primary white--text">
                    <span>BotMan Widget</span>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="isOpen = false">
                        <v-icon color="white">mdi-close</v-icon>
                    </v-btn>
                </v-card-title>

                <v-card-text class="chat-messages" ref="messageContainer">
                    <div class="welcome-message">
                        <div class="message-bubble bot-message">
                            <p>Hi!</p>
                            <p>Welcome to the BotMan documentation! 😊</p>
                            <p>
                                Most of the code samples and snippets throughout
                                the documentation work in this widget - feel
                                free to give it a try!
                            </p>
                            <small class="message-time">{{
                                getCurrentTime()
                            }}</small>
                        </div>
                    </div>

                    <div
                        v-for="(message, index) in messages"
                        :key="index"
                        class="message-wrapper"
                    >
                        <div
                            :class="[
                                'message-bubble',
                                message.isUser ? 'user-message' : 'bot-message',
                            ]"
                        >
                            <p>{{ message.text }}</p>
                            <small class="message-time">{{
                                message.time
                            }}</small>
                        </div>
                    </div>
                </v-card-text>

                <v-card-actions class="chat-input">
                    <v-text-field
                        v-model="newMessage"
                        placeholder="Send a message..."
                        @keyup.enter="sendMessage"
                        hide-details
                        outlined
                        dense
                    >
                        <template v-slot:append>
                            <v-btn
                                icon
                                @click="sendMessage"
                                :disabled="!newMessage"
                            >
                                <v-icon>mdi-send</v-icon>
                            </v-btn>
                        </template>
                    </v-text-field>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name: "ChatWidget",

    data() {
        return {
            isOpen: false,
            newMessage: "",
            messages: [],
            chatHistory: [],
        };
    },

    methods: {
        getCurrentTime() {
            return new Date().toLocaleTimeString([], {
                hour: "2-digit",
                minute: "2-digit",
            });
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            const userMessage = {
                text: this.newMessage,
                isUser: true,
                time: this.getCurrentTime(),
            };

            this.messages.push(userMessage);
            this.chatHistory.push({
                role: "user",
                content: this.newMessage,
            });

            const messageToSend = this.newMessage;
            this.newMessage = "";

            // Scroll to bottom
            this.$nextTick(() => {
                if (this.$refs.messageContainer) {
                    this.$refs.messageContainer.scrollTop =
                        this.$refs.messageContainer.scrollHeight;
                }
            });

            try {
                const response = await this.sendToAPI(messageToSend);

                if (response.success) {
                    this.messages.push({
                        text: response.message,
                        isUser: false,
                        time: this.getCurrentTime(),
                    });

                    this.chatHistory = response.history;
                } else {
                    this.messages.push({
                        text: "Sorry, I encountered an error. Please try again.",
                        isUser: false,
                        time: this.getCurrentTime(),
                    });
                }
            } catch (error) {
                console.error("Chat error:", error);
                this.messages.push({
                    text: "Sorry, I encountered an error. Please try again.",
                    isUser: false,
                    time: this.getCurrentTime(),
                });
            }

            // Scroll to bottom again after response
            this.$nextTick(() => {
                if (this.$refs.messageContainer) {
                    this.$refs.messageContainer.scrollTop =
                        this.$refs.messageContainer.scrollHeight;
                }
            });
        },

        async sendToAPI(message) {
            try {
                const response = await axios.post(this.API + "/chat", {
                    message,
                    history: this.chatHistory,
                });
                return response.data;
            } catch (error) {
                console.error("API error:", error);
                throw error;
            }
        },
    },
};
</script>

<style scoped>
.chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.chat-widget-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
}

.chat-container {
    display: flex;
    flex-direction: column;
    height: 600px;
}

.chat-header {
    padding: 12px 20px !important;
}

.chat-messages {
    flex-grow: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f5f5f5;
    height: calc(100% - 120px);
}

.message-wrapper {
    margin: 10px 0;
}

.message-bubble {
    padding: 10px 15px;
    border-radius: 15px;
    max-width: 80%;
    word-wrap: break-word;
}

.message-bubble p {
    margin: 0;
    padding: 0;
}

.bot-message {
    background: #e3f2fd;
    margin-right: auto;
    border-bottom-left-radius: 5px;
}

.user-message {
    background: #e8f5e9;
    margin-left: auto;
    border-bottom-right-radius: 5px;
}

.message-time {
    display: block;
    font-size: 0.7rem;
    margin-top: 5px;
    opacity: 0.7;
}

.chat-input {
    padding: 10px;
    background: white;
}

.welcome-message {
    margin-bottom: 20px;
}
</style>
