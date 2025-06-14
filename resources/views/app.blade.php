<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Template Project - Admin</title>

    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <v-app v-cloak v-if="!adminLogin">
            <confirm ref="confirm"></confirm>
            <confirm ref="dialog"></confirm>
            <alert ref="alert"></alert>
            <loading ref="loading" :dialog="loading_dialog"></loading>
            <error-dialog v-model="err_dialog" :err_dialog="err_dialog"></error-dialog>

            <register-page v-if="is_register"></register-page>
            <forgot-password-page v-else-if="is_forgot"></forgot-password-page>
            <reset-password-page v-else-if="is_reset"></reset-password-page>
            <login-page v-else></login-page>
        </v-app>
        <v-app v-cloak v-else>
            <confirm ref="confirm"></confirm>
            <alert ref="alert"></alert>
            <loading ref="loading" :dialog="loading_dialog"></loading>
            <cloacking ref="cloacking" :dialog="cloacking_dialog"></cloacking>
            <error-dialog v-model="err_dialog" :err_dialog="err_dialog"></error-dialog>

            <v-snackbar v-model="snackbar.notification" right top :color="snackbar.type" :timeout="snackbar.timeout"
                small>
                <i><b>@{{ snackbar.notificationText }}</b></i>

                <v-btn v-if="snackbar.hasButton" text depressed small :color="snackbar.buttonColor"
                    @click="snackbar.onClick" class="ml-4">
                    @{{ snackbar.buttonText }}
                </v-btn>
            </v-snackbar>

            <v-navigation-drawer id="app-drawer" left :permanent="!$vuetify.breakpoint.sm && !$vuetify.breakpoint.xs"
                :fixed="!$vuetify.breakpoint.sm && !$vuetify.breakpoint.xs" v-model="drawer" v-if="!!pageReady" app
                :key="'nav-' + navDrawerKey">

                <template v-slot:prepend>
                    <v-subheader class="px-0 py-15">
                        <v-img class="mx-auto" src="/img/logo.png" max-width="156"></v-img>
                    </v-subheader>
                </template>

                <v-list nav dense class="px-8" :key="'list-' + listKey" id="drawer-list">
                    <template v-for="item in items">

                        <v-list-item v-if="!item.hasChild" :key="item.title + item.name" :to="item.link"
                            class="mx-0 px-2 mt-1" active-class="tab-selected">
                            <v-icon :class="`${ item.color }--text mr-3 ml-1`">
                                @{{ item.icon }}
                            </v-icon>
                            <v-list-item-content fluid class="px-0 mx-0">
                                <div v-text="item.title"></div>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-group v-else active-class="parent-active" :key="item.title + item.name" no-action
                            :group="getRegex(item.group)" class="px-0 mx-0" fluid :class="`${ item.color }--text`">
                            <template v-slot:activator>
                                <v-list-item class="mx-0 px-0 mt-1">
                                    <v-icon :class="`${ item.color }--text mr-3 ml-1`">
                                        @{{ item.icon }}
                                    </v-icon>

                                    <v-list-item-content fluid class="px-0 mx-0">
                                        <div class="menu-activator" v-text="item.title"></div>
                                    </v-list-item-content>
                                </v-list-item>
                            </template>

                            <v-list-item v-for="child in item.children" :key="child.title + child.name"
                                :to="child.link" :class="child.class">
                                <v-list-item-action :class="`${ child.color }--text`">
                                    <v-icon>@{{ child.icon }}</v-icon>
                                </v-list-item-action>
                                <v-list-item-content fluid class="pl-2">
                                    <div v-text="child.title"></div>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-group>
                    </template>
                </v-list>

                <template v-slot:append>
                    <v-list nav dense class="px-8" id="logout-list">
                        <v-list-item @click.stop="$root.logout" style="margin-bottom: 4px !important;">
                            <v-list-item-action class="my-0">
                                <span style="font-size: 24px" class="ma-0 mdi mdi-logout-variant mdi-flip-h"></span>
                            </v-list-item-action>
                            <v-list-item-content>
                                <v-list-item-title>Logout</v-list-item-title>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>
                </template>
            </v-navigation-drawer>

            <v-main style="background-color: #F6F7FB !important;">
                <v-sheet id="scrolling-techniques-7" class="overflow-y-auto" max-height="100vh">
                    <v-container min-height="100vh" fluid style="width: 100%; background-color: #F6F7FB !important;">
                        <v-layout row wrap>
                            <router-view ref="child"></router-view>
                        </v-layout>
                    </v-container>
                </v-sheet>
            </v-main>
        </v-app>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
