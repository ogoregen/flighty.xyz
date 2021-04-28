# Database Structure

## users
| Name | Type | Null | Default | Extra |
| :--- | :--- | :--- | :--- | :--- |
| id | int(11) | No | _None_ | AUTO_INCREMENT PRIMARY |
| username | varchar(255) | No | _None_ | |
| password | varchar(60) | No | _None_ | |

## pages
| Name | Type | Null | Default | Extra |
| :--- | :--- | :--- | :--- | :--- |
| id | int(11) | No | _None_ | AUTO_INCREMENT PRIMARY |
| menu_index | int(11) | No | _None_ | |
| title | varchar(255) | No | _None_ | |
| content | text | No | _None_ | |
| content_raw | text | No | _None_ | |

## articles
| Name | Type | Null | Default | Extra |
| :--- | :--- | :--- | :--- | :--- |
| id | int(11) | No | _None_ | AUTO_INCREMENT PRIMARY |
| creation_date | datetime | No | current_timestamp() | |
| title | varchar(255) | No | _None_ | |
| description | text | Yes | _NULL_ | |
| content | text | No | _None_ | |
| content_raw | text | No | _None_ | |

## newsletter_subscribers
| Name | Type | Null | Default | Extra |
| :--- | :--- | :--- | :--- | :--- |
| id | int(11) | No | _None_ | AUTO_INCREMENT PRIMARY |
| email | varchar(255) | No | _None_ | |
