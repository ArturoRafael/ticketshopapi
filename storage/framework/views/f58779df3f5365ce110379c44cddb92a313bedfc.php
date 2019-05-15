```bash
curl -X <?php echo e($route['methods'][0]); ?> <?php echo e($route['methods'][0] == 'GET' ? '-G ' : ''); ?>"<?php echo e(trim(config('app.docs_url') ?: config('app.url'), '/')); ?>/<?php echo e(ltrim($route['boundUri'], '/')); ?>" <?php if(count($route['headers'])): ?>\
<?php $__currentLoopData = $route['headers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    -H "<?php echo e($header); ?>: <?php echo e($value); ?>"<?php if(! ($loop->last) || ($loop->last && count($route['bodyParameters']))): ?> \
<?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php if(count($route['cleanBodyParameters'])): ?>
    -d '<?php echo json_encode($route['cleanBodyParameters']); ?>'
<?php endif; ?>

```
