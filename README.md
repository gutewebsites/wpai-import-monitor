# WP All Import - Import Monitor

This plugin monitors when an import was last started and provides this information for each import job ID as JSON, including the number of updated, deleted, and skipped entries.

## Description

The WP All Import - Import Monitor plugin extends the functionality of WP All Import by monitoring import events and storing information about the import time, the number of updated, deleted, and skipped entries for each import job ID. This information can be retrieved in JSON format via a REST API.

## Features

- Monitors the last import time for each import job ID.
- Stores the number of updated, deleted, and skipped entries.
- Provides a REST API to retrieve the collected data as JSON.

## Minimum Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- MySQL 5.0 or higher

## Installation

1. Upload the plugin files to the `/wp-content/plugins/wpai-import-monitor` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

1. Once the plugin is activated, it will automatically monitor each import job.
2. After running an import, you can retrieve the data via the REST API.
3. Access the URL `/wp-json/wpai/v1/last_import_times` to get the import information as JSON.

### Example JSON Response:

```json
{
    "1": {
        "last_import_time": "2024-10-01 12:34:56",
        "updated": 10,
        "deleted": 2,
        "skipped": 5
    },
    "2": {
        "last_import_time": "2024-10-02 13:45:01",
        "updated": 7,
        "deleted": 3,
        "skipped": 0
    }
}
