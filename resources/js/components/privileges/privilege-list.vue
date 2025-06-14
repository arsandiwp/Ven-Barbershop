<template>
    <v-container grid-list-md pt-10>
        <HeaderBar />
        <paginated-data-list
            :key="list_key"
            :api="api"
            :list="list"
        ></paginated-data-list>
    </v-container>
</template>

<script>
import PaginatedDataList from "../paginate-data-list";

export default {
    name: "privileges",
    components: {
        PaginatedDataList,
    },
    props: {
        api: {
            type: String,
            required: true,
        },
    },

    // beforeCreate() {
    //     let needAdm = true;
    //     let returnTo = document.referrer;
    //     if(document.referrer != '/privileges'){
    //         returnTo = '/';
    //     }
    //     if(localStorage.is_admin == 'false' && needAdm) window.location.replace(returnTo)
    //     else safe = true;
    // },

    created() {
        this.devLog("Privileges Page Created...");
        this.initData();
    },

    watch: {
        "$route.path": function (path) {
            this.devLog("watching change");
            this.initData();
        },

        "list.paginate.itemsPerPage"(val, old) {
            this.devLog(old + "-" + val);

            if (old != val && (val != "300" || val != 300)) {
                if (this.list.loading != true) {
                    this.updatePage(val);
                }
            }
        },
    },

    data() {
        return {
            list_key: 1,
            list: {
                title: "Privileges List",
                search: "",
                datas: [],
                headers: [],
                loading: false,
                data_loaded: false,
            },
        };
    },

    methods: {
        initData() {
            this.devLog("Initialize Privileges Page");
            this.initHead();
        },

        initHead() {
            this.list.headers = [];
            this.list.headers = [
                {
                    text: "No.",
                    value: "index",
                    align: "center",
                    sortable: false,
                },
                {
                    text: "Privilage",
                    type: "text",
                    value: "name",
                    align: "left",
                    // sortable: !this.printable,
                    sortable: false,
                },
                {
                    text: "Description",
                    type: "text",
                    value: "description",
                    align: "left",
                    // sortable: !this.printable,
                    sortable: false,
                },
            ];
        },
    },

    mounted() {},
};
</script>
