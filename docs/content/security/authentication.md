# Authentication

Speedtest Tracker uses Filament for the admin panel. During the install process an admin account is created for you.

### Default Login Account

During the first start of the application a default admin account is created for you:

| Username            | Password   |
| ------------------- | ---------- |
| `admin@example.com` | `password` |

!!! info

    You can set your own login details for this account with the [`ADMIN_NAME`, `ADMIN_EMAIL` and `ADMIN_PASSWORD`](../installation/environment-variables.md#application) environment variables. They're only used when the account is created on the first start, so set them before starting the container for the first time.

### Change Login Account

#### Login Details

You can update the login details of your account through the profile page. Every user can update these details for their own account.

* In the top right corner click on the user logo next to the bell icon.
* Click on Profile
* Change the `Name`, `E-Mail Address` and `Password` to your liking.

#### Change Account details

As an Admin you can change the account details of other accounts.

* On the right side menu click on `Users`
* Click on user account you want to change
* Change the `Name`, `E-Mail Address` ,`Password` and `Role` to your liking.

### Create Login Account

You can create additional user accounts.

* On the right side menu click on `Users`
* Click on `New User`
* Fill in the `Name`, `E-Mail Address, Password` and `Password confirmation` to your liking.
* Choose the needed role for the user under `Role`.

!!! info

    The difference between the Roles can be found in the [Authorization](authorization.md) section.
