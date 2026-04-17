<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Country;
use App\Models\MemberInstitution;

use Image;

class UserController extends Controller
{

    public function __construct(){
        $this->middleware('can:users.index')->only('index');
        $this->middleware('can:users.edit')->only('edit', 'update');
    }

    public function index(){
        // $category_name = '';
        $data = [
            'category_name' => 'users',
            'page_name' => 'users',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        //get search value
        $listforpage = request()->query('listforpage') ?? 10;
        $search = request()->query('search');

        $users = User::where(function ($query) use ($search) {
                // Búsqueda por nombre completo o primer nombre y primer apellido
                $search = str_replace(' ', '%', $search);

                $query->where('id', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('status', 'LIKE', "%{$search}%")
                      ->orWhereRaw('CONCAT(COALESCE(name, ""), " ", COALESCE(lastname, ""), " ", COALESCE(second_lastname, "")) LIKE ?', ["%{$search}%"]);
            })
            ->whereIn('status', ['active', 'inactive'])
            ->orderBy('id', 'desc')
            ->paginate($listforpage);



        return view('pages.user.index')->with($data)->with('users',$users);
    }

    public function create(){
        // $category_name = '';
        $data = [
            'category_name' => 'users',
            'page_name' => 'userscreate',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $roles = Role::all();

        // $pageName = 'analytics';
        return view('pages.user.create')->with($data)->with('roles',$roles);
    }

    public function store(Request $request)
    {
        $users = new User();

        $request->validate(
            [
                'name'              =>      'required|string',
                'email'             =>      'required|email|unique:users,email',
                'password'          =>      'required|alpha_num|min:6'
            ]
        );

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $photouser = rand(11111111, 99999999) . $image->getClientOriginalName();

            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(300, 300);
            $image_resize->save(storage_path('app/public/uploads/profile_images/' . $photouser));
        } else {
            $photouser = 'default-profile.jpg';
        }


        $users->name = $request->name;
        $users->lastname = '';
        $users->second_lastname = '';
        $users->document_type = '';
        $users->document_number = '';
        $users->email = $request->email;
        $users->password = bcrypt($request->password);
        $users->photo = $photouser;
        $users->status = $request->get('status');

        $users->save();

        $users->assignRole($request->input('roles'));

        return redirect()->route('users.index');
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        // $category_name = '';
        $data = [
            'category_name' => 'users',
            'page_name' => 'usersedit',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $memberinstitutions = MemberInstitution::all();

        $user = User::find($id);

        $roles = Role::all();
        $countries = Country::all();

        // $pageName = 'analytics';
        return view('pages.user.edit')->with($data)->with('user',$user)->with('memberinstitutions', $memberinstitutions)->with('roles',$roles)->with('countries',$countries);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
            if ($request->get('password') != '') {
                $pass = bcrypt($request->get('password'));
            } else {
                $pass = $user->password;
        }

        $currentImage = $user->photo;

        if($request->file('photo') != '') {
            $image = $request->file('photo');
            $path = public_path() . '/assets/img';
            $photouser = rand(11111111, 99999999) . $image->getClientOriginalName();
            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(500, 500);
            $image_resize->save(storage_path('app/public/uploads/profile_images/' . $photouser));
        } else {
            $photouser = $currentImage;
        }

        User::whereId($id)->update([
            'salutation' => $request['salutation'],
            'name' => $request['name'],
            'lastname' => $request['lastname'],
            'second_lastname' => $request['second_lastname'],
            'degrees' => $request['degrees'],
            'other_degrees' => $request['other_degrees'],
            'is_cugh_member' => $request['is_cugh_member'],
            'cugh_member_institution' => $request['cugh_member_institution'],
            'job_title' => $request['job_title'],
            'email' => $request['email'],
            'cc_email' => $request['cc_email'],
            'document_type' => $request['document_type'],
            'document_number' => $request['document_number'],
            'nationality' => $request['nationality'],
            'gender' => $request['gender'],
            'occupation' => $request['occupation'],
            'occupation_other' => $request['occupation_other'],
            'workplace' => $request['workplace'],
            'address' => $request['address'],
            'city' => $request['city'],
            'state' => $request['state'],
            'country' => $request['country'],
            'work_phone_code' => $request['work_phone_code'],
            'work_phone_code_city' => $request['work_phone_code_city'],
            'work_phone_number' => $request['work_phone_number'],
            'phone_code' => $request['phone_code'],
            'phone_number' => $request['phone_number'],
            'whatsapp_code' => $request['whatsapp_code'],
            'whatsapp_number' => $request['whatsapp_number'],
            'solapin_name' => $request['solapin_name'],
            'solapin_lastname' => $request['solapin_lastname'],
            'password' => $pass,
            'photo' => $photouser,
        ]);

        $userinfo = User::find($id);
        $userinfo->roles()->sync($request->roles);

        return redirect()->route('users.edit', ['user' => $id])->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        //cambiar el status a deleted
        $user = User::findOrFail($id);
        $user->status = 'deleted';
        $user->save();

        return redirect()->route('users.index');
    }


    public function myprofile(){
        $id = \Auth::user()->id;
        // $category_name = '';
        $data = [
            'category_name' => 'profile',
            'page_name' => 'myprofile',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $countries = Country::all();
        $user = User::find($id);
        $roles = Role::all();

        // $pageName = 'analytics';
        return view('pages.user.myprofile')->with($data)->with('user',$user)->with('roles',$roles)->with('countries',$countries);
    }

    public function updatemyprofile(Request $request){
        $id = \Auth::user()->id;

        $user = User::findOrFail($id);
            if ($request->get('password') != '') {
                $pass = bcrypt($request->get('password'));
            } else {
                $pass = $user->password;
        }

        $currentImage = $user->photo;

        if($request->file('photo') != '') {
            $image = $request->file('photo');
            $path = public_path() . '/assets/img';
            $photouser = rand(11111111, 99999999) . $image->getClientOriginalName();
            $image_resize = Image::make($image->getRealPath());
            $image_resize->resize(500, 500);
            $image_resize->save(public_path('/assets/img/' . $photouser));
        } else {
            $photouser = $currentImage;
        }

        User::whereId($id)->update([
            'name' => $request['name'],
            'lastname' => $request['lastname'],
            'second_lastname' => $request['second_lastname'],
            'document_type' => $request['document_type'],
            'document_number' => $request['document_number'],
            'country' => $request['country'],
            'state' => $request['state'],
            'city' => $request['city'],
            'address' => $request['address'],
            'postal_code' => $request['postal_code'],
            'phone_code' => $request['phone_code'],
            'phone_code_city' => $request['phone_code_city'],
            'phone_number' => $request['phone_number'],
            'whatsapp_code' => $request['whatsapp_code'],
            'whatsapp_number' => $request['whatsapp_number'],
            'workplace' => $request['workplace'],
            'photo' => $photouser,
            'solapin_name' => $request['solapin_name'],
        ]);


        return redirect()->back();
    }

}
