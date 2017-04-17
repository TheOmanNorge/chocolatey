<?php

namespace App\Http\Controllers;

use App\Facades\Nux;
use App\Facades\User as UserFacade;
use App\Facades\Validation;
use App\Models\NuxValidation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class NuxController
 * @package App\Http\Controllers
 */
class NuxController extends BaseController
{
    /**
     * Check an User Name.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkName(Request $request): JsonResponse
    {
        if (User::where('username', $request->json()->get('name'))->where('id', '<>', UserFacade::getUser()->uniqueId)->count() > 0) {
            return response()->json(new NuxValidation('NAME_IN_USE'));
        }

        if (!Validation::filterUserName($request->json()->get('name'))) {
            return response()->json(new NuxValidation('INVALID_NAME', ['resultType' => 'VALIDATION_ERROR_ILLEGAL_WORDS']));
        }

        return response()->json(new NuxValidation);
    }

    /**
     * Select an User Name.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function selectName(Request $request): JsonResponse
    {
        UserFacade::updateSession(['username' => $request->json()->get('name')]);

        return response()->json(new NuxValidation);
    }

    /**
     * Select a Room.
     *
     * @param Request $request
     * @return Response
     */
    public function selectRoom(Request $request): Response
    {
        if (!in_array($request->json()->get('roomIndex'), [1, 2, 3])) {
            return response('', 400);
        }

        UserFacade::updateSession(['traits' => (Nux::generateRoom($request) ? ['USER'] : ['USER', 'NEW_USER'])]);

        return response(null, 200);
    }
}
