@extends('welcome')
@section('title',"Specialist Referrals")
@section('content')

<div id="home-content" class="container"> 
    <div  class=" justify-content-center  mt-4">
      <h2>Specialist Referrals</h2>
      <h4 class="mt-4"><i>
        Skin
      </i></h4>  
    </div>
    <div class="row gy-3 mt-4">
      <div class="col-md-4">
        <div class="card">
          <img class="card-img-top" src="..." alt="Card image cap">
          <div class="card-body">
            <h5>Dermatology Referral For Acne</h5>
            <p>A doctor referral to a dermatologist for acne. </p>
            <span style="background-color: lightblue;font-weight: bold;" class="text-white rounded px-2 py-1">price: $12</span>

            <a href="#" class="btn btn-primary w-100 mt-3">Request Refferal</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <img class="card-img-top" src="..." alt="Card image cap">
          <div class="card-body">
            <h5>Dermatology Referral For General Skin/Mole Check</h5>
            <p>A doctor referral to a dermatologist for a skin/mole check to check for melanoma and other skin cancers.</p>

            <a href="#" class="btn btn-primary w-100 mt-3">Request Refferal</a>
          </div>
        </div>
      </div>
    </div>

    <h4 class="mt-4"><i>
        Eyes
    </i></h4>  

    <div class="row gy-3 mt-4">
      <div class="col-md-4">
        <div class="card">
          <img class="card-img-top" src="..." alt="Card image cap">
          <div class="card-body">
            <h5>Ophthalmologist Referral For Age-Related Macular Degeneration</h5>
            <p> For people over 50 years of age to check for any vision loss. </p>

            <a href="#" class="btn btn-primary w-100 mt-3">Request Refferal</a>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <img class="card-img-top" src="..." alt="Card image cap">
          <div class="card-body">
            <h5>Ophthalmologist Referral For Cataracts</h5>
            <p> To check for cataracts which can cause visual problems. </p>

            <a href="#" class="btn btn-primary w-100 mt-3">Request Refferal</a>
          </div>
        </div>
      </div>

      
      <div class="col-md-4">
        <div class="card">
          <img class="card-img-top" src="..." alt="Card image cap">
          <div class="card-body">
            <h5>Ophthalmologist Referral For Diabetes</h5>
            <p> For diabetics, who should get an eye check to screen for Diabetic Retinopathy (eye disease).  </p>

            <a href="#" class="btn btn-primary w-100 mt-3">Request Refferal</a>
          </div>
        </div>
      </div>
    </div>

    <div class="row gy-3 mt-4">
        <div class="col-md-4">
          <div class="card">
            <img class="card-img-top" src="..." alt="Card image cap">
            <div class="card-body">
              <h5>Ophthalmologist Referral For Glaucoma</h5>
              <p> To review or check for signs of glaucoma which can cause vision loss. </p>
  
              <a href="#" class="btn btn-primary w-100 mt-3">Request Refferal</a>
            </div>
          </div>
        </div>
    </div>

    <h4 class="mt-4"><i>
        Colonoscopy
    </i></h4>  

    <div class="row gy-3 mt-4">
      <div class="col-md-4">
        <div class="card">
          <img class="card-img-top" src="..." alt="Card image cap">
          <div class="card-body">
            <h5>Gastroenterology (Colonoscopy) Referral For Initial Screen</h5>
            <p>For patients over 50 years of age with risk factors such as family history or bowel symptoms like rectal bleeding or altered bowel habit. </p>

            <a href="#" class="btn btn-primary w-100 mt-5">Request Refferal</a>
          </div>
        </div>
      </div>
    </div>

>
      
</div>
  @endsection
