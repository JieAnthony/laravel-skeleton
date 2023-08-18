<?php

test('true', function () {
    expect(true)->toBeTrue();
});

test('sum', function () {
    $result = bcadd(1, 2,2);

    $this->assertSame("3.00", $result); // Same as expect($result)->toBe(3)
});