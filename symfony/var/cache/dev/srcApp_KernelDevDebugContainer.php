<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\Container3g596Kd\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/Container3g596Kd/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/Container3g596Kd.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\Container3g596Kd\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \Container3g596Kd\srcApp_KernelDevDebugContainer([
    'container.build_hash' => '3g596Kd',
    'container.build_id' => 'c431acd5',
    'container.build_time' => 1559638464,
], __DIR__.\DIRECTORY_SEPARATOR.'Container3g596Kd');
