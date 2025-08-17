<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@latest/css/materialdesignicons.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900|Material+Icons|Material+Icons+Outlined" rel="stylesheet">
        
        <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">

        <link rel="stylesheet" href="https://unpkg.com/@egjs/flicking/dist/flicking.css" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://unpkg.com/@egjs/flicking/dist/flicking-inline.css" crossorigin="anonymous" />

        <link rel="stylesheet" href="https://naver.github.io/egjs-flicking-plugins/release/latest/dist/flicking-plugins.css">

        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css"
        />

        <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>
        <style>
            .no-shadow.v-btn{
                box-shadow: none !important;
                width: 100px;
            }
            .round-left.v-btn{
                border-top-left-radius: 5px !important;
                border-bottom-left-radius: 5px !important;
            }
            .round-right.v-btn{
                border-top-right-radius: 5px !important;
                border-bottom-right-radius: 5px !important;
            }
            .v-tooltip__content {
                pointer-events: initial;
            }
        </style>
        <link href="<?php echo e(asset('css/app.css')); ?>" rel="stylesheet">
    </head>
    <body>
        <div id="customer_app" v-cloak>
            <v-app>
                <confirm ref="confirm"></confirm>
                <alert ref="alert"></alert>
                <loading ref="loading" :dialog="loading_dialog"></loading>
                <cloacking ref="cloacking" :dialog="cloacking_dialog"></cloacking>
                <error-dialog v-model="err_dialog" :err_dialog="err_dialog"></error-dialog>
                <snackbar :snackbar="snackbar"></snackbar>

                <v-main style="background-color:white;">
                    
                                <router-view v-if="!needReload()" :key="this.$route.path" ref="child"></router-view>
                            
                </v-main>

            </v-app>
        </div>
        <script src="<?php echo e(asset('js/customer_app.js')); ?>"></script>
    </body>
</html>
<?php /**PATH /Users/arsandiwirapanorama/Downloads/Ven-Barbershop/resources/views/customer_app.blade.php ENDPATH**/ ?>