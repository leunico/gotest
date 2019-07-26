<?php

declare(strict_types=1);

namespace Modules\Operate\Entities\Traits;

use Modules\Operate\Events\OrderChange;

trait OrderByCourse
{
    public function courseHandle()
    {
        if ($this->save()) {
            event(new OrderChange($this));

            return true;
        }

        return false;
    }
}
