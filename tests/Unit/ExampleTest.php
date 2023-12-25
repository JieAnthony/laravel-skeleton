<?php

test('true', function () {
    expect(true)->toBeTrue();
});

test('sum', function () {
    $result = bcadd(1, 1, 2);

    expect($result)->toBeString('2.00');
});