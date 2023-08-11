<?php

it('request root', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
});

it('request api', function () {
    $response = $this->get('/api');

    $response->assertStatus(200);
});
