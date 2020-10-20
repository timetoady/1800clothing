// JavaScript File - Create by Victor Chukhry
"use strict";
/*global let*/
/*global $*/
/*global axios*/
/*global Vue*/


Vue.component('my-component', {
    template: '<script>',

    mounted: function() {
        var self = this;
        $('.mydatatable').DataTable({});
    },

});






let vm = new Vue({
    el: '#app',

    data: {
        allCostumes: []
    },

    created: function() {
        this.getAllData();
    },


    methods: {
        process: function(event) {
            //event.preventDefault();
            alert(this.test);
        },

        getAllData: function() {
            axios({
                url: 'https://carma-viktorc.c9users.io/carma/public/costumes',
                method: 'get'
            }).then(function(response) {
                console.log(response);
                vm.allCostumes = response.data;
            });
        },
    }


});