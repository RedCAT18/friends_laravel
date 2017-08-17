<?php

namespace App\Http\Controllers;

use App\UserUser;
use Illuminate\Http\Request;
use \App\User;

class UserController extends Controller
{
    //
    public function index(Request $request){
        $user = User::all();

        return $user;
    }

    public function selectUser(Request $request){
//        $id = $request->all();
        //본인 제외
//        $user = User::whereNotIn('id', $request)->get();

        //제외할 아이디값들을 찾는다. (본인, 내가 추가한 친구, 나를 추가한 친구)
        $self = User::findOrFail($request);
        $data = array();
        $data2 = array();

        //내가 친구를 등록한 경우를 검색
        foreach($self[0]->friends as $friend){
            $data[] = $friend->pivot->friend_id;
        }
        //친구가 나를 등록한 경우를 검색
        foreach($self[0]->users as $user){
            $data2[] = $user->pivot->user_id;
        }
        $data = array_merge($data, $data2);

        array_push($data, $self[0]->id);

        $user = User::whereNotIn('id', $data)->get();

        return $user;
    }

    public function save(Request $request){
//        클라이언트로부터 받아온 사용자의 아이디와 친구추가할 아이디들
        $userId =  $request->userId;
        $friendsId = $request->friendsId;
//        \DB::enableQueryLog();

        //아이디로 해당 유저를 찾고, 받아온 친구의 아이디를 받아 관계테이블에 저장.
//        $user = User::findOrFail($userId);

        foreach((array) $friendsId as $friend) {
            $conn = new UserUser();

            $conn->user_id = $userId;
            $conn->friend_id = $friend;

            $conn->save();
        }

//        return \DB::getQueryLog();

//        $user->friends()->attach($friendsId);

        return response()->json(['success'=>1]);
    }

    public function showList(Request $request){
        $self = User::findOrFail($request);

        $data = array();
        $data2 = array();

        foreach($self[0]->friends as $friend){
            $data[] = $friend->pivot->friend_id;
        }
        //친구가 나를 등록한 경우를 검색
        foreach($self[0]->users as $user){
            $data2[] = $user->pivot->user_id;
        }
        $data = array_merge($data, $data2);

        $user = User::whereIn('id', $data)->get();

        return $user;
    }

    public function destroy(Request $request){
        $userId =  $request->userId;
        $friendsId = $request->friendsId;

//        \DB::enableQueryLog();
        //유저가 친구를 추가한 상태는 삭제가 되는데

//        $user = User::findOrFail($userId);
//        $user->friends()->detach($friendsId);

        foreach($friendsId as $friend){
            $disc1 = UserUser::where('user_id','=',$userId)->where('friend_id','=',$friend)->delete();
            $disc2 = UserUser::where('friend_id','=',$userId)->where('user_id','=',$friend)->delete();
        }

        //친구가 유저를 추가한 상태는 어떻게 삭제하는가........?
        //번거롭지만 친구 데이터를 하나하나 조회한 다음 그 친구 아이디에 유저가 있는 경우를 골라 삭제
//        foreach($friendsId as $friend){
//            $each = User::findOrFail($friend);
//            $each->friends()->detach($userId);
//        }

//        return \DB::getQueryLog();

        return response()->json(['success'=>1]);


    }
}
