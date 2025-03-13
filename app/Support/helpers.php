<?php

if (! \function_exists('getPageLimit')) {
    function getPageLimit(int|string|null $limit = null)
    {
        if (! $limit) {
            return config('eloquentfilter.paginate_limit');
        }

        if (\is_string($limit)) {
            $limit = (int) $limit;
        }

        if ($limit <= 0) {
            return config('eloquentfilter.paginate_limit');
        }

        return \min($limit, 50);
    }
}
