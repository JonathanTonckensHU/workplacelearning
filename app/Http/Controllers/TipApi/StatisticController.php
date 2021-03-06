<?php

namespace App\Http\Controllers\TipApi;

use App\Http\Requests\StatisticStoreRequest;
use App\Tips\Statistics\CustomStatistic;
use App\Tips\Statistics\Statistic;
use App\Tips\StatisticService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatisticController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param StatisticStoreRequest $request
     * @param StatisticService $statisticService
     * @return \App\Tips\Statistics\CustomStatistic
     * @throws \Exception
     */
    public function store(StatisticStoreRequest $request, StatisticService $statisticService)
    {
        return $statisticService->createStatistic($request->all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws \Exception
     * @param StatisticStoreRequest $request
     * @param  int $id
     * @param StatisticService $statisticService
     * @return Statistic
     */
    public function update(StatisticStoreRequest $request, $id, StatisticService $statisticService)
    {
        /** @var CustomStatistic $statistic */
        $statistic = (new CustomStatistic)->findOrFail($id);
        $statisticService->updateStatistic($statistic, $request->all());

        return $statistic;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($id)
    {
        /** @var CustomStatistic $statistic */
        $statistic = (new Statistic)->findOrFail($id);
        $statistic->coupledStatistics()->delete();

        $statistic->delete();

        return response()->json([], 200);
    }
}
