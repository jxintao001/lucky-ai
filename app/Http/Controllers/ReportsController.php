<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Transformers\ReportTransformer;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        if (!$request['type']) {
            throw new ResourceException('type不能为空');
        }
        $reports = Report::where('type', $request['type'])
            ->orderByDesc('order')
            ->paginate(per_page());
        return $this->response()->paginator($reports, new ReportTransformer());
    }

    public function show($id)
    {
        $report = Report::findOrFail($id);
        return $this->response()->item($report, new ReportTransformer());
    }

}