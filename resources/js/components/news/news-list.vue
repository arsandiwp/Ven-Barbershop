<template>
    <v-container grid-list-md pt-10>
        <HeaderBar/>
        <PaginatedDataList
            :key="list_key"
            :api="api"
            :list="list"
        />
    </v-container>
</template>

<style scoped>

</style>

<script>
import PaginatedDataList from "../paginate-data-list";
import HeadAvatar from "../head-avatar";

export default {
    name: "news-list",
    components: {
        HeadAvatar,
        PaginatedDataList,
    },
    props: {
        api: {
            type: String,
            required: true,
        },
        apiCUD: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            list_key: 0,
            list: {
                title: "News",
                search: "",
                add: this.addItem,
                edit: null,
                detail: null,
                datas: [],
                headers: [],
                loading: false,
                data_loaded: false,
                statuses: ["All", "Draft", "Published"],
                filter_status: "All",
                actions: [
                    {
                        label: "View News",
                        icon: "mdi-file-find",
                        color: "primary",
                        rules: [
                            { key: 'status', value: 'Published'},
                        ],
                        fx: this.openPreview,
                    },
                    {
                        label: "Edit News",
                        icon: "mdi-pencil-outline",
                        color: "warning",
                        fx: this.editItem,
                    },
                    {
                        label: "Delete News",
                        icon: "mdi-trash-can-outline",
                        color: "error",
                        fx: this.deleteItem,
                    },
                ],
            },
        };
    },

    watch: {
        "$route.path": function (path) {
            this.devLog("watching change");
            this.initData();
        },
    },

    created() {
        this.devLog("Component News List Created");
        this.initData();
    },

    mounted() {
        
    },

    methods: {
        initData() {
            this.devLog("Initialize Socmed Setting Page");
            if (!!this.$route.params.snackbarOpt) {
                let snackbarOpt = this.$route.params.snackbarOpt;
                this.simpleSnackbar(snackbarOpt.text, snackbarOpt.type);
            }
            this.initHead();
        },

        initHead() {
            this.list.headers = [
                { text: "No.", value: "index", align: "center", width: "100", sortable: false },
                { text: "Main Title", value: "title", align: "left", sortable: false},
                { text: "Main Image", preview: true, field:"image_url", value: "image", align: "center", sortable: false },
                { text: "Date", value: "date", align: "center", sortable: false},
                // { text: "Category", parent:"category", field:'name', value: "parent", align: "left", sortable: false},
                // { text: "Header", value: "header", align: "left", sortable: false},
                // { text: "Admin", parent:"user", field:'name', value: "parent", align: "center", sortable: false},
                { text: "Status", value: "status", align: "center", sortable: false},
                { text: "Action", value: "actions", align: "center", sortable: false },
            ];
        },

        openPreview(item){
            let x = item.id
            if(item.custom_url && item.custom_url != ""){
                x = item.custom_url;
            }
            window.open('/news/'+x, '_blank');
        },

        addItem(){
            this.$router.push({path: '/news/add' });
        },

        editItem(item){
            this.$router.push({path: '/news/edit/'+item.id });
        },

        deleteItem(item){
            this.confirm("Delete News", "Are you sure?", {
                color: "error",
            }).then((confirm) => {
                if (confirm) {
                    this.deleteData(item);
                } else {
                    this.devLog("not confirmed");
                }
            });
        },

        deleteData(item) {
            this.LOADING(true);
            const index = this.list.datas.indexOf(item);

            let api = this.apiCUD + "/" + item.id;
            axios
                .delete(api, { headers: { Authorization: localStorage.token } })
                .then((response) => {
                    this.list.datas.splice(index, 1);
                    this.devLog(JSON.stringify(response));
                    if (response.status) {
                        this.simpleSnackbar(
                            "Item has been deleted!!",
                            "success"
                        );
                    } else {
                        this.showErr(response);
                    }
                })
                .catch((err) => {
                    if (err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                })
                .finally(() => {
                    this.LOADING(false);
                });
        },
    },
};
</script>