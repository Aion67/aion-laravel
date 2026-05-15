# Implementation Outline

## Data

- Expand the seeder to create realistic users, customers, medications, inventory levels, prescriptions, sales, and stock movements.
- Generate sales over the last 6 months so reporting charts have meaningful series data.

## Reports

- Extend report controllers to provide chart-friendly aggregates.
- Keep totals, top items, and trend data in the controller so Blade stays presentation-focused.

## UI

- Add reusable Blade components for chart cards and export actions.
- Use a lightweight front-end chart library for bar, line, and pie charts.

## Exports

- Serve CSV downloads from the report controller.
- Let charts export themselves as PNG images from the browser canvas.
