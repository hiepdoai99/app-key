<?php
declare(strict_types=1);

namespace App\Traits;

use App\Models\PlanSubscription;
use Illuminate\Support\Facades\Auth;

trait HtqJwtResponse
{

    protected function respondWithTokenAndSubscription(string $token, PlanSubscription $planSubscription)
    {
        return $this->respondWithToken($token, $planSubscription);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, PlanSubscription $planSubscription = null)
    {
        $res = [
            'apiToken' => $token,
            'tokenType' => 'Bearer',
            'expiresIn' => Auth::factory()->getTTL()
        ];
        if (!is_null($planSubscription)) {
            $res['his'] = $planSubscription->his;
            $day = $planSubscription->getSubscriptionPeriodRemainingUsageIn('day');
            $res['remainingDay'] = $day === 0 ? $planSubscription->getTrialPeriodRemainingUsageIn('day') : $day;
        }
        return response()->json($res, 200);
    }
}
