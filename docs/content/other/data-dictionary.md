# Data Dictionary

### Tables

#### Results

| Field | Type | Description |
| --- | --- | --- |
| `id` | primary key |  |
| `service` | string | Service user to run the speedtest. |
| `ping` | double | As milliseconds |
| `download` | unsigned big int | As bytes |
| `upload` | unsigned big int | As bytes |
| `comments` | text | User added comments. |
| `data` | json | The raw response from the speedtest. |
| `benchmarks` | json | Captures the speedtest's benchmarks at run time. |
| `healthy` | boolean | Indicates if the speedtest was healthy compared to the benchmark. |
| `status` | string | • **Completed** - a speedtest that ran successfully.<br>• **Failed** - a speedtest that failed to run successfully.<br>• **Started** - a speedtest that has been started but has not finished running.<br>• **Skipped** - a speedtest that was skipped. See message for more details. |
| `scheduled` | boolean | Was the result scheduled. |
| `created_at` | timestamp | When the record was created. |
| `updated_at` | timestamp | When the record was last updated. |
