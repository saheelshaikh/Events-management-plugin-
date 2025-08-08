# Simple Events Manager

**Author:** Mohammed Saheel Shaikh  
**Version:** 1.0  
**Description:** A lightweight WordPress plugin to manage events (create, view, edit, delete) and display them on the frontend using a shortcode.

---

## 📌 Features
- Custom Post Type: **Events** with title, description, date, location, and organizer.
- Event details stored as custom fields.
- Default **Location** and **Organizer** settings in the WordPress admin panel.
- Shortcode `[events_list]` to display upcoming events.
- Filters events to show only future events.

---

## 📥 Installation
1. Download or clone this repository.
2. Create a folder named `simple-events-manager` in your `wp-content/plugins` directory.
3. Place the `simple-events-manager.php` file inside this folder.
4. In your WordPress dashboard, go to **Plugins → Installed Plugins**.
5. Find **Simple Events Manager** and click **Activate**.

---

## ⚙️ Usage

### **Adding Events**
1. Go to **Events → Add New**.
2. Enter the title and description.
3. In the **Event Details** meta box, set:
   - Date
   - Location
   - Organizer  
   (If left blank, defaults from settings will be used)
4. Publish the event.

### **Setting Defaults**
1. Go to **Settings → Events Settings**.
2. Enter the default location and organizer.
3. Save changes.

### **Displaying Events**
- Use the shortcode:
```php
[events_list]
