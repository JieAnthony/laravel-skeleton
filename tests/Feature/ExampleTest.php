<?php

use function Pest\Laravel\get;

it('request root', function () {
    get('/')->assertStatus(302);
});

it('request api', function () {
    get('/api')->assertStatus(200);
});
