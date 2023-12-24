@extends('admin.layouts.app')

@section('content')
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Create Cupons</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        <form action="" method="post" id="cuponForm" enctype="multipart/form-data" name="cuponForm">
                        <div class="card">
							<div class="card-body">								
								<div class="row">
									<div class="col-md-6">
										<div class="mb-3">
											<label for="name">Code</label>
											<input type="text" name="code" id="code" class="form-control" placeholder="Coupon Code">
                                            <p></p>	
										</div>
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label for="slug">Name</label>
											<input type="text" name="name" id="name" class="form-control" placeholder="Coupon Code Name">
                                            <p></p>		
										</div>
									</div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="image">Description</label>
                                            <textarea  name="description" id="description" class="form-control" placeholder="Description">
                                         </div>
                                    </div>

                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="slug">Max Uses</label>
											<input type="number" name="max_uses" id="max_uses" class="form-control" placeholder="Max Uses">
                                            <p></p>		
										</div>
									</div>

                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="slug">Max Uses User</label>
											<input type="number" name="max_uses_user" id="max_uses_user" class="form-control" placeholder="Max Uses User">
                                            <p></p>		
										</div>
									</div>

                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="status">Type</label>
                                            <select name="type" id="type" class="form-control">
                                                    <option value="percent">Percent</option>
                                                    <option value="fixed">Fixed</option>

                                            </select>
										</div>
									</div>

                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="slug">Discount Amount</label>
											<input type="text" name="discount_amount" id="discount_amount" class="form-control" placeholder="Discount Amount">
                                            <p></p>		
										</div>
									</div>

                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="slug">Min Discount Amount</label>
											<input type="text" name="min_amount" id="min_amount" class="form-control" placeholder="Min Discount Amount">
                                            <p></p>		
										</div>
									</div>

                                 <div class="col-md-6">
										<div class="mb-3">
											<label for="status">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="0">Block</option>

                                            </select>
										</div>
									</div>

                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="slug">Start At</label>
											<input type="text" name="starts_at" id="starts_at" class="form-control" placeholder="Start At">
                                            <p></p>		
										</div>
									</div>

                                    <div class="col-md-6">
										<div class="mb-3">
											<label for="slug">Expire At</label>
											<input type="text" name="expire_at" id="expire_at" class="form-control" placeholder="Expire At">
                                            <p></p>		
										</div>
									</div>
                                   										
								</div>
							</div>							
						</div>
						<div class="pb-5 pt-3">
							<button type="submit" class="btn btn-primary">Create</button>
							<a href="{{route('categories.index')}}" class="btn btn-outline-dark ml-3">Cancel</a>
						</div>
                        </form>
					</div>
					<!-- /.card -->
				</section>
  @endsection
  
  @section('customJs')
    <script type="text/javascript">
        $("#categoryForm").submit(function(event){
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled',true);

            $.ajax({
                url: '{{route("categories.store")}}',
                type:'post',
                data: element.serializeArray(),
                dataType:'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled',false);
                    
                    if(response["status"] == true){
                        window.location.href="{{route('categories.index')}}"

                        $("#name").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");

                        $("#slug").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");

                    } else{

                        var errors = response['errors'];
                    if(errors['name']){
                        $("#name").addClass('is-invalid')
                        .siblings('p').addClass('invalid-feedback')
                        .html(errors['name']);
                    } else{
                        
                        $("#name").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");
                    
                    }

                    if(errors['slug']){
                        $("#slug").addClass('is-invalid')
                        .siblings('p').addClass('invalid-feedback')
                        .html(errors['slug']);
                        
                    } else{

                        $("#slug").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");
                    }

                    }

                   

                }, error: function(jqXHR, exception){
                        console.log("Something wrong");
                }
            });
        });


        $("#name").change(function(){

            element = $(this);
            $("button[type=submit]").prop('disabled',true);
        $.ajax({
                url: '{{ route("getSlug") }}',
                type:'get',
                data: {title: element.val()},
                dataType:'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled',false);
                    if(response["status"] == true){
                        $("#slug").val(response["slug"]);
                    }
                }
            });
            });


            Dropzone.autoDiscover = false;    
       const dropzone = $("#image").dropzone({ 
      init: function() {
        this.on('addedfile', function(file) {
            if (this.files.length > 1) {
                this.removeFile(this.files[0]);
            }
        });
    },
    url:  "{{ route('temp-images.create') }}",
    maxFilesize: 2,
     paramName: 'image',
    addRemoveLinks: true,
    // dataType: 'json',
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }, 
    success: function(file, response){
       $("#image_id").val(response.image_id);
         console.log(response);
     },
    error: function(file, response){
        console.log(response);
    }
});
    </script>
  @endsection