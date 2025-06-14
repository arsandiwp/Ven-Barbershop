<template>
    <v-container fluid>
        <!-- <v-row justify="center" v-if="loading">
            <v-progress-circular
                indeterminate
                color="primary"
                size="64"
            ></v-progress-circular>
        </v-row> -->

        <!-- <v-row v-else> -->
        <v-row>
            <v-col cols="12" md="3">
                <!-- Sidebar with chat sessions -->
                <v-card>
                    <v-card-title class="py-2">
                        <v-icon left>mdi-forum</v-icon>
                        Chat History
                        <v-spacer></v-spacer>
                        <v-btn icon small @click="createNewSession">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </v-card-title>

                    <v-divider></v-divider>
                    <v-list dense>
                        <v-list-item
                            v-for="(session, index) in chatSessions"
                            :key="index"
                            @click="selectSession(session.session_id)"
                            :class="{
                                'selected-session':
                                    session.session_id === currentSessionId,
                            }"
                        >
                            <v-list-item-icon>
                                <v-icon>mdi-chat</v-icon>
                            </v-list-item-icon>
                            <v-list-item-content>
                                <v-list-item-title>
                                    {{ session.title ?? "Empty Session" }}
                                </v-list-item-title>
                                <v-list-item-subtitle>
                                    {{
                                        formatSessionDate(session.last_activity)
                                    }}
                                </v-list-item-subtitle>
                            </v-list-item-content>
                            <v-list-item-action>
                                <v-btn
                                    icon
                                    x-small
                                    @click.stop="deleteItem(session.session_id)"
                                >
                                    <v-icon>mdi-delete</v-icon>
                                </v-btn>
                            </v-list-item-action>
                        </v-list-item>
                    </v-list>
                </v-card>
            </v-col>

            <v-col cols="12" md="9">
                <!-- Main chat area -->
                <v-card height="90vh" class="d-flex flex-column">
                    <v-card-title class="py-2">
                        <v-icon left>mdi-chat-processing</v-icon>
                        {{ currentSessionTitle }}
                        <!-- <v-spacer></v-spacer> -->
                        <!-- <v-select
                            v-model="selectedModel"
                            :items="availableModels"
                            label="Model"
                            dense
                            outlined
                            hide-details
                            class="model-select"
                            style="max-width: 200px"
                        ></v-select> -->
                    </v-card-title>
                    <v-divider></v-divider>

                    <!-- Chat messages -->
                    <v-card-text
                        class="chat-messages flex-grow-1"
                        ref="chatContainer"
                    >
                        <div v-if="loading" class="text-center my-5">
                            <v-progress-circular
                                indeterminate
                                color="primary"
                            ></v-progress-circular>
                        </div>
                        <template v-else>
                            <div
                                v-if="messages.length === 0"
                                class="text-center grey--text my-5"
                            >
                                <v-icon large class="mb-3"
                                    >mdi-chat-question</v-icon
                                >
                                <div>
                                    No messages yet. Start a conversation!
                                </div>
                            </div>
                            <template v-else>
                                <div
                                    v-for="(message, index) in messages"
                                    :key="index"
                                    class="my-3"
                                >
                                    <v-card
                                        :class="[
                                            'message-card',
                                            message.role === 'user'
                                                ? 'user-message'
                                                : 'assistant-message',
                                        ]"
                                        flat
                                    >
                                        <v-card-title class="py-1 px-3">
                                            <v-avatar size="24" class="mr-2">
                                                <v-icon size="18">{{
                                                    message.role === "user"
                                                        ? "mdi-account"
                                                        : "mdi-robot"
                                                }}</v-icon>
                                            </v-avatar>
                                            {{
                                                message.role === "user"
                                                    ? "You"
                                                    : "Assistant"
                                            }}
                                        </v-card-title>
                                        <v-card-text class="py-2 px-3">
                                            <div
                                                v-html="
                                                    formatMessage(
                                                        message.content
                                                    )
                                                "
                                            ></div>
                                        </v-card-text>
                                    </v-card>
                                </div>
                            </template>
                        </template>
                    </v-card-text>

                    <!-- Input area -->
                    <v-card-actions class="input-area">
                        <v-textarea
                            v-model="userMessage"
                            outlined
                            hide-details
                            rows="1"
                            auto-grow
                            placeholder="Type your message..."
                            @keydown.enter.prevent="sendMessage"
                            :disabled="loading"
                        ></v-textarea>
                        <v-btn
                            color="primary"
                            fab
                            small
                            class="ml-2"
                            @click="sendMessage"
                            :disabled="loading || !userMessage.trim()"
                        >
                            <v-icon>mdi-send</v-icon>
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
import { marked } from "marked";
import DOMPurify from "dompurify";

export default {
    name: "ChatPage",
    data() {
        return {
            chatSessions: [],
            currentSessionId: null,
            messages: [],
            userMessage: "",
            loading: false,
            selectedModel: null,
            availableModels: [],
            // loading: false
        };
    },
    computed: {
        currentSessionTitle() {
            if (!this.currentSessionId) return "New Chat";
            const session = this.chatSessions.find(
                (s) => s.session_id === this.currentSessionId
            );
            return session ? session.title : "Chat";
        },
    },
    mounted() {
        this.fetchChatSessions();
    },
    methods: {
        fetchChatSessions() {
            axios
                .get(this.API + "/chat/sessions", {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.chatSessions = response.data.sessions;

                    // if (
                    //     this.chatSessions.length > 0 &&
                    //     !this.currentSessionId
                    // ) {
                    //     this.selectSession(this.chatSessions[0].session_id);
                    // }
                })
                .catch((err) => {
                    if (!!err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                });
        },

        selectSession(sessionId) {
            this.loading = true;

            axios
                .get(this.API + `/chat/history/${sessionId}`, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.currentSessionId = sessionId;
                    this.messages = response.data.history || [];
                    this.scrollToBottom();
                })
                .catch((err) => {
                    if (!!err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        createNewSession() {
            this.currentSessionId = null;
            this.messages = [];
            this.userMessage = "";
        },

        deleteItem(sessionId) {
            document.activeElement.blur();
            this.confirm(
                "Delete Data",
                "Are you sure you want to delete User [" + sessionId + "]?",
                {
                    nohead: false,
                    color: "error",
                    agree: "Delete",
                    cancel: "Cancel",
                    width: 300,
                }
            ).then((confirm) => {
                if (confirm) {
                    this.deleteSession(sessionId);
                } else {
                    this.devLog("not confirmed");
                }
            });
        },

        deleteSession(sessionId) {
            axios
                .delete(this.API + `/chat/sessions/${sessionId}`, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.chatSessions = this.chatSessions.filter(
                        (s) => s.session_id !== sessionId
                    );

                    if (this.currentSessionId === sessionId) {
                        this.createNewSession();
                    }
                })
                .catch((err) => {
                    if (!!err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                });
        },

        sendMessage() {
            if (!this.userMessage.trim() || this.loading) return;

            this.loading = true;
            const message = this.userMessage;
            this.userMessage = "";

            this.messages.push({
                role: "user",
                content: message,
            });

            this.scrollToBottom();

            axios
                .post(
                    this.API + "/chat",
                    {
                        message: message,
                        session_id: this.currentSessionId,
                    },
                    {
                        headers: { Authorization: localStorage.token },
                    }
                )
                .then((response) => {
                    if (!this.currentSessionId) {
                        this.currentSessionId = response.data.session_id;
                        this.fetchChatSessions();
                    }

                    this.messages = response.data.history;
                    this.scrollToBottom();
                })
                .catch((err) => {
                    if (!!err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        formatSessionDate(dateString) {
            if (!dateString) return "";
            const date = new Date(dateString);
            return date.toLocaleDateString();
        },

        formatMessage(content) {
            const html = marked(content);
            return DOMPurify.sanitize(html);
        },

        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.chatContainer) {
                    this.$refs.chatContainer.scrollTop =
                        this.$refs.chatContainer.scrollHeight;
                }
            });
        },
    },
};
</script>

<style scoped>
.chat-messages {
    overflow-y: auto;
    padding: 16px;
    background-color: #f5f5f5;
}

.message-card {
    border-radius: 12px;
    margin-bottom: 12px;
    max-width: 85%;
}

.user-message {
    margin-left: auto;
    background-color: #e3f2fd !important;
}

.assistant-message {
    margin-right: auto;
    background-color: white !important;
}

.input-area {
    padding: 12px;
    background-color: white;
    border-top: 1px solid #e0e0e0;
    display: flex;
    align-items: flex-end;
}

.selected-session {
    background-color: #e3f2fd;
}

.model-select {
    font-size: 14px;
}
</style>
