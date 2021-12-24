# Core

The core module is being refactored from a set of granular modules which were
almost always on to one larger monolithic module which is always on.

This simplifies dependency tracking, making sure the right things are enabled,
crafting module configuration pages, role/permission management, and so on.