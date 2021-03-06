<?php
/************************************
 * Purpose : Member , Individual , MarketPlace Registrations
 * Author : Nikhil Kishore
 * Company : Logistiks
 * Description : In the controller we are handling the member registration process of logistiks.
 * Created At : 05th Aug 2016
 * 
 *
 */
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Console\IlluminateCaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Models\Registration;
//use App\Http\Requests\Request;
//use Illuminate\Http\Request;
//use App\Http\Requests;
use App\Components\EmailSender;
use Illuminate\Http\Request;
use App\Models\Login;





class LoginController extends Controller
{
 /*
  * Method Name : 
  * Purpose :
  * 
  */
  public function __construct(Login $login,EmailSender $emailSender){

       $this->login = $login;
       $this->sendemail = $emailSender;

  }
  public function checkAuth(Request $request){
    try{
           
          $this->validate($request,['login_email'=>'required|email:true','login_password'=>'required|min:8|max:16']);

          $email = Input::get('login_email');
          $password  = Input::get('login_password'); 
          $password = md5($password);

          $authenticate = $this->login->authenticateUser($email,$password);


          if(!empty($authenticate)){
            
            Session::put('user_id',$authenticate[0]->id);
            Session::put('user_name',$authenticate[0]->user_name);
            
            if($authenticate[0]->seller_buyer_flag == 2){
              
              return Redirect::to('/SellerRateCard');

              

            }
            else{

              return Redirect::to('/buyer/search');
              
            }


          }
          else{
             
             $errorMsg = 'Invalid email or password';
             
             return Redirect::to('memberRegistration')->withErrors($errorMsg);
          }

      }
    catch(Exception $ex){

      return $ex->getMessage();
    
    }
  }  

  public function dashboard(){

    return view('dashboard');
  }

  public function forgotPassword(){

      return view('forgotpassword');
  }

  public function sendMail(){
      try{

            $inputs = Input::all();
            
            return $this->sendemail->sendMailToUser($inputs);


          }
         catch(Exception $ex){

          return $ex->getMessage();
         
         }

  }

  public function inviteUser(){

      $session_id = Session::get('user_id');

      $getInviteusers = $this->login->getInviteusers($session_id);

      return view('inviteuser',['getInviteusers'=>$getInviteusers]);

  }

  public function logOut(){

     Session::put('user_id'," ");

     return Redirect::to('memberRegistration');


  }

 
 }
