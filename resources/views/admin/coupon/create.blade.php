@extends('admin.layouts.app')

@section('content')
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Create Coupons</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{route('coupons.index')}}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        <form action="" method="post" id="discountForm" enctype="multipart/form-data" name="discountForm">
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

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="image">Description</label>
                                            <textarea  name="description" id="description" class="form-control" placeholder="Description"></textarea>
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

            $(document).ready(function(){
                  $('#starts_at').datetimepicker({
                    // options here
                    format:'Y-m-d H:i:s',
                });

                $('#expire_at').datetimepicker({
                    // options here
                    format:'Y-m-d H:i:s',
                });
            });


        $("#discountForm").submit(function(event){
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled',true);

            $.ajax({
                url: '{{route("coupons.store")}}',
                type:'post',
                data: element.serializeArray(),
                dataType:'json',
                success: function(response){
                    $("button[type=submit]").prop('disabled',false);
                    
                    if(response["status"] == true){
                        window.location.href="{{route('coupons.index')}}"

                        $("#code").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");

                        $("#discount_amount").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");

                        $("#starts_at").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");

                        $("#expire_at").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");

                    } else{

                        var errors = response['errors'];
                    if(errors['code']){
                        $("#code").addClass('is-invalid')
                        .siblings('p').addClass('invalid-feedback')
                        .html(errors['code']);
                    } else{
                        
                        $("#code").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");
                    
                    }

                    if(errors['discount_amount']){
                        $("#discount_amount").addClass('is-invalid')
                        .siblings('p').addClass('invalid-feedback')
                        .html(errors['discount_amount']);
                        
                    } else{

                        $("#discount_amount").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");
                    }

                    if(errors['starts_at']){
                        $("#starts_at").addClass('is-invalid')
                        .siblings('p').addClass('invalid-feedback')
                        .html(errors['starts_at']);
                        
                    } else{

                        $("#starts_at").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");
                    }

                    if(errors['expire_at']){
                        $("#expire_at").addClass('is-invalid')
                        .siblings('p').addClass('invalid-feedback')
                        .html(errors['expire_at']);
                        
                    } else{

                        $("#expire_at").removeClass('is-invalid')
                        .siblings('p').removeClass('invalid-feedback')
                        .html("");
                    }

                    }

                   

                }, error: function(jqXHR, exception){
                        console.log("Something wrong");
                }
            });
        });

    </script>
  @endsection