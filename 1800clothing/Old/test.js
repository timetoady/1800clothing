Vue.component('data-table', {
    render: function(createElement) {
        return createElement(
            "table", null, []
        )
    },
    props: ['comments'],
    data() {
        return {
            headers: [
                { title: 'Name' },
                { title: 'Email' },
                { title: 'Body' },
            ],
            rows: [],
            dtHandle: null
        }
    },
    watch: {
        comments(val, oldVal) {
            let vm = this;
            vm.rows = [];
            val.forEach(function(item) {
                let row = [];
                row.push(item.name);
                row.push('<a href="mailto://' + item.email + '">' + item.email + '</a>');
                row.push(item.body);
                vm.rows.push(row);
            });
            vm.dtHandle.clear();
            vm.dtHandle.rows.add(vm.rows);
            vm.dtHandle.draw();
        }
    },
    mounted() {
        let vm = this;
        vm.dtHandle = $(this.$el).DataTable({
            columns: vm.headers,
            data: vm.rows,
            searching: true,
            paging: true,
            info: false
        });
    }
});

new Vue({
    el: '#tabledemo',
    data: {
        comments: [],
        search: ''
    },
    computed: {
        filteredComments: function() {
            let self = this
            let search = self.search.toLowerCase()
            return self.comments.filter(function(comments) {
                return comments.name.toLowerCase().indexOf(search) !== -1 ||
                    comments.email.toLowerCase().indexOf(search) !== -1 ||
                    comments.body.toLowerCase().indexOf(search) !== -1
            })
        }
    },
    mounted() {
        let vm = this;
        $.ajax({
            url: 'https://jsonplaceholder.typicode.com/comments',
            success(res) {
                vm.comments = res;
            }
        });
    }
});




// $(document).ready(function() {
//     $('#data-table').DataTable( {
//         "processing": true,
//         "serverSide": true,
//         "ajax": {
//             "url": "https://carma-viktorc.c9users.io/carma/public/costume/5",
//             "type": "GET"
//         },
//         data: data,
//         "columns": [
//             { data: "id" },
//             { data: "image" },
//             { data: "thumbnail" }

//         ]
//     } );
// } ); 













// $(document).ready(function() {
//  $('#data-table').DataTable({
//   "ajax": {
//     "url": "https://carma-viktorc.c9users.io/carma/public/costumes",
//     method: 'get',
//     dataType: 'json',
//     "dataSrc": "data"
//   }
//  });
// });






// $(document).ready(function() {
//     $('#data-table').DataTable({
//         "ajax": "/carma/templates/test.json",
//         "columns": [
//             { "data": "name" },
//             { "data": "gender" },
//             { "data": "designation" }
//         ]
//     });
// });









// $(document).ready(function() {
//  $('.mydatatable').DataTable({
//   "ajax": "/carma/templates/test.json",

//   "columns": [
//    { "data": "id" },
//    { "data": "image" },
//    { "data": "thumbnail" },
//    { "data": "year_from" },
//    { "data": "year_to" },
//    { "data": "person" },
//    { "data": "clothing" },
//    { "data": "caption" },
//    { "data": "description" },
//    { "data": "source" }
//   ]
//  });

// });


// $(document).ready(function() {

//     $.ajax({
//         // url: 'https://carma-viktorc.c9users.io/carma/public/costume/5',
//         url: '/carma/templates/test.json',
//         method: 'get',
//         dataType: 'json',
//         success: function(data) {
//          console.log(data),
//             $('#data-table').DataTable({
//                 data: data,
//                 columns: [
//                     { data: "name" }, 
//                     { data: "clothing" },
//                     { data: "description" } 
//                     // { "data": "year_from" }, 
//                     // { "data": "year_to" }, 
//                     // { "data": "person" },
//                     // { "data": "clothing" },
//                     // { "data": "caption" },
//                     // { "data": "description" },
//                     // { "data": "source" }
//                  ]
//             });
//         }
//     });






// $(document).ready(function() {
//  $.getJSON("https://carma-viktorc.c9users.io/carma/public/costume/5", function(data) {
//   let temp_data = '';
//   $.each(data, function(key, value) {
//    temp_data += '<tr>';
//    temp_data += '<td>' + value.image + '</td>';
//    temp_data += '</tr>';
//   });
//   $('#data-table').append(temp_data);
//   console.log(data);
//  });
//  $('#data-table').DataTable({});
// });