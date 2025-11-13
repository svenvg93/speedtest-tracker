<?php

return [
    'title' => 'Data integratie',
    'label' => 'Data integratie',

    // InfluxDB v2
    'influxdb_v2' => 'InfluxDB v2',
    'influxdb_v2_description' => 'Wanneer ingeschakeld, zullen alle nieuwe Snelheids resultaten ook worden verzonden naar de InfluxDB.',
    'influxdb_v2_enabled' => 'Inschakelen',
    'influxdb_v2_url' => 'URL',
    'influxdb_v2_url_placeholder' => 'http://your-influxdb-instance',
    'influxdb_v2_org' => 'Org',
    'influxdb_v2_bucket' => 'Emmer',
    'influxdb_v2_bucket_placeholder' => 'snelheid-tracker',
    'influxdb_v2_token' => 'Token',
    'influxdb_v2_verify_ssl' => 'Controleer SSL',

    // Actions
    'export_current_results' => 'Huidige resultaten exporteren',
    'test_connection' => 'Verbindingstest testen',
    'starting_bulk_data_write_to_influxdb' => 'bulkgegevens schrijven naar InfluxDB starten',
    'sending_test_data_to_influxdb' => 'Testgegevens verzenden naar InfluxDB',

    // Common labels (can be removed if they're in general.php)
    'org' => 'Org',
    'bucket' => 'Emmer',
    'token' => 'Token',
];
