<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Http\Requests\Community\ShowcasesRequest;
use App\Models\ArtChannel;
use App\Models\Privacy;
use App\Models\Showcase;
use App\Models\ShowcaseArt;
use Illuminate\Http\Request;

class ShowcasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'Lấy danh sách thành công',
            'list' => (function() use ($request) {
                $list = Showcase::where('user_id',$request->user()->id)->with(['showcase_arts' => function($query) {
                    $query->inRandomOrder()->with('arts');
                }])->orderBy('created_at','desc')->get(); 
                return $list;
            })(),
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShowcasesRequest $request)
    {
        $showcase_data = [
            'title' => $request->title,
            'subheading' => $request->subheading,
            'description' => $request->description,
            'privacy_id' => $request->privacy,
            'art_channel_id' => $request->channel,
            'user_id' => $request->user()->id,
        ];

        $createShowcase = Showcase::create($showcase_data);

        if ($createShowcase) {
            $art_ids = explode(',',$request->art_ids_list);
            $showcase_id = $createShowcase->id;
            $relationships = [];

            foreach ($art_ids as $art_id) {
                array_push($relationships,[
                    'art_id' => (int) $art_id,
                    'showcase_id' => $showcase_id,
                    'user_id' => $request->user()->id,
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ]);
            }

            $createRelationships = ShowcaseArt::insert($relationships);

            if (!$createRelationships) {
                return response()->json([
                    'message' => 'Tạo quày thất bại',
                ],500);
            }
        } else {
            return response()->json([
                'message' => 'Tạo quày thất bại',
            ],500);
        }
        
        return response()->json([
            'message' => 'Tạo thành công',
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json([
            'message' => 'Đã lấy Showcase thành công',
            'showcase' => Showcase::with('showcase_arts.arts.artChannels','showcase_arts.arts.dimensions','users')->find($id),
            'channelSelectList' => ArtChannel::all(),
            'privacySelectList' => Privacy::all(),
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->removal_list !== null) {
            $ids = explode(',',$request->removal_list);
            $removeArtFromShowcase = ShowcaseArt::whereIn('id',$ids)->delete();
            if (!$removeArtFromShowcase) {
                return response()->json([
                    'message' => 'Gỡ ảnh thất bại',
                ],500);
            }
        }

        if (ShowcaseArt::where('showcase_id',$id)->count() > 0) {
            $showcase = Showcase::find($id);
            $showcase->title = $request->title;
            $showcase->subheading = $request->subheading;
            $showcase->description = $request->description;
            $showcase->art_channel_id = $request->channel;
            $showcase->privacy_id = $request->privacy;
            $save = $showcase->save();
            if (!$save) {
                return response()->json([
                    'message' => 'Cập nhật thất bại',
                ],500);
            }
        } else {
            Showcase::destroy($id);
            return response()->json([
                'message' => 'Đã xóa quày vì không còn tác phẩm nào',
                'redirect' => true,
            ]);
        }


        return response()->json([
            'message' => 'Cập nhật thành công',
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ShowcaseArt::where('showcase_id',$id)->delete();
        Showcase::destroy($id);
        return response()->json([
            'message' => 'Đã xóa quày',
        ]);
    }
}
