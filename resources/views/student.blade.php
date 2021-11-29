<!DOCTYPE html>
<html>
<head>
    <title>Laravel 6 Ajax CRUD Example</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.all.min.js"></script>
</head>
<body>

<div class="container">
    <h1>Laravel 6 Ajax CRUD </h1>
    <a class="btn btn-success" href="javascript:void(0)" id="createNewStudent"> Create New Student</a>
    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Address</th>
                <th>Phone</th>
                <th width="280px">Action</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
            </div>
            <div class="modal-body">
                <form id="studentForm" name="studentForm" class="form-horizontal">
                   <input type="hidden" name="student_id" id="student_id">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address" class="col-sm-2 control-label">Address</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone" value="" maxlength="50" required="">
                        </div>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10">
                     <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                     </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>

<script type="text/javascript">
  $(function () {

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'address', name: 'address'},
            {data: 'phone', name: 'phone'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });

    $('#createNewStudent').click(function () {
        $('#saveBtn').val("create-student");
        $('#student_id').val('');
        $('#studentForm').trigger("reset");
        $('#modelHeading').html("Create New Student");
        $('#ajaxModel').modal('show');
    });

    $('body').on('click', '.editStudent', function () {
      var student_id = $(this).data('id');
      $.get("{{ route('student.index') }}" +'/' + student_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Student");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal('show');
          $('#student_id').val(data.id);
          $('#name').val(data.name);
          $('#address').val(data.address);
          $('#phone').val(data.phone);
      })
   });

    $('#saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html('Sending..');

        $.ajax({
          data: $('#studentForm').serialize(),
          url: "{{ route('student.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {

              $('#studentForm').trigger("reset");
              $('#ajaxModel').modal('hide');
              table.draw();

          },
          error: function (data) {
              console.log('Error:', data);
              $('#saveBtn').html('Save Changes');
          }
      });
    });

    $('body').on('click', '.deleteStudent', function () {

        var student_id = $(this).data("id");
        var confirmation = confirm("Are You sure want to delete ?");

        if (confirmation) {
            $.ajax({
                type: "DELETE",
                url: "{{ route('student.index') }}"+'/'+student_id,
                success: function (data) {
                    table.draw();
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
        }

        // function confirmDelete() {
        //     swal({
        //         title: "Are you sure?",
        //         text: "You will not be able to recover this imaginary file!",
        //         type: "warning",
        //         showCancelButton: true,
        //         confirmButtonColor: "#DD6B55",
        //         confirmButtonText: "Yes, delete it!",
        //         closeOnConfirm: false
        //     }, function (isConfirm) {
        //         if (!isConfirm) return;
        //         $.ajax({
        //             type: "DELETE",
        //             url: "{{ route('student.index') }}"+'/'+student_id,
        //             success: function (data) {
        //                 swal("Done!", "It was succesfully deleted!", "success");
        //             },
        //             error: function (xhr, ajaxOptions, thrownError) {
        //                 swal("Error deleting!", "Please try again", "error");
        //             }
        //         });
        //     });
        // }

        // swal({
        //     title: "Are you sure?",
        //     text: "If you delete this post all associated comments also deleted permanently.",
        //     type: "warning",
        //     showCancelButton: true,
        //     closeOnConfirm: false,
        //     showLoaderOnConfirm: true,
        //     confirmButtonClass: "btn-danger",
        //     confirmButtonText: "Yes, delete it!",
        // }, function(isConfirm) {
        //     //     $.ajax({
        //     //     type: "DELETE",
        //     //     url: "{{ route('student.index') }}"+'/'+student_id,
        //     //     success: function (data) {
        //     //         console.log('ini');
        //     //         table.draw();
        //     //     },
        //     //     error: function (data) {
        //     //         console.log('Error:', data);
        //     //     }
        //     // });
        //     console.log('ini');
        // });
    });

  });
</script>
</html>