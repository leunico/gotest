<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait ExceptionRenderable
{
    use Helpers;

    public function report(): void
    {
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render(Request $request): Response
    {
        return $this->response()->error($this->getMessage());
    }
}
