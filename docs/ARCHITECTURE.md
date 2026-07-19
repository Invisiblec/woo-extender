# Architecture in Action

Here is a simplified example of how data flows through the application layers using our defined standards:

```php
namespace WooExtender\Controllers;

use WooExtender\Validation\DataValidator;
use WooExtender\DTO\BatchData;
use WooExtender\Services\InventoryService;

final class BatchController
{
    public function __construct(
        private readonly InventoryService $inventoryService,
        private readonly DataValidator $validator
    ) {}

    /**
     * Handles the request to create a new inventory batch.
     */
    public function create( array $requestData ): array
    {
        // 1. Validation Layer
        $validated = $this->validator->validate($requestData);

        // 2. Data Transfer Object (DTO)
        $batchData = BatchData::fromArray($validated);

        // 3. Domain Service (Business Logic & Persistence)
        $batch = $this->inventoryService->registerNewBatch($batchData);

        // 4. Response
        return [
            'success' => true,
            'message' => __('Batch created successfully.', 'woo-extender'),
            'data'    => $batch->toArray(),
        ];
    }
}
```

This pattern ensures controllers remain thin, business rules are isolated, and dependencies are clearly injected.
