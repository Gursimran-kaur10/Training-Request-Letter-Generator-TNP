# Training Request Letter Generator

A Moodle **local plugin** for students to submit training details and request a Training and Placement Cell letter. Moodle stores requests in its own database. Coordinators review each request, add an optional note, and approve or reject it. Students can preview or download a PDF only after approval.

This is a small plugin project, not a standalone website. It must be installed inside a working Moodle site. It uses Moodle's PHP APIs and database connection; it does not create its own database connection or need Node.js.

## Features in this project

- Student form using Moodle's Form API (`moodleform`).
- Prefills the student's Moodle full name and ID number when available; the student can edit both.
- Saves student, company, training and status details using Moodle's `$DB` Database API.
- Database-neutral table definition in `db/install.xml`.
- Student sees their own request history and its approval status.
- Coordinator sees all requests and can set Pending, Approved or Rejected with an optional note.
- Moodle capability checks control student submission and coordinator review.
- Approved requests have an in-browser PDF preview and a PDF download.
- PDF uses the PDF library bundled with Moodle; no extra PDF package is required.
- English interface text is kept in the Moodle language file.

## Requirements

- A working Moodle site, version **4.1 or later** (the minimum version declared by `version.php`).
- PHP and a database supported by that Moodle installation.
- A Moodle administrator account for plugin installation.
- A student account to submit a sample request and a coordinator/manager account to approve it.

The task describes this as a Moodle local plugin under `local/training_letter`. The plugin uses Moodle's own database, so it works with the database configured by the host Moodle site (for example PostgreSQL or MySQL).

## Folder structure

```text
moodle-training-letter/
├── classes/
│   └── form/
│       ├── request_form.php    # Student request fields
│       └── review_form.php     # Coordinator status and note form
├── db/
│   ├── access.php              # Moodle capabilities and default role grants
│   └── install.xml             # Portable request-table definition
├── lang/
│   └── en/
│       └── local_training_letter.php # All English UI strings
├── apply.php                   # Student application form and save logic
├── getletter.php               # Permission-checked PDF preview/download
├── index.php                   # Blue-and-white portal home page
├── lib.php                     # Adds links to Moodle navigation
├── manage.php                  # Coordinator request dashboard
├── myrequests.php              # Current user's request history
├── review.php                  # Approve/reject one request
├── styles.css                  # Small responsive blue-and-white styles
├── version.php                 # Plugin name, version and Moodle requirement
└── README.md                   # Setup and code guide
```

### What the important files do

| File | Explanation |
| --- | --- |
| `version.php` | Moodle uses the component name and version to identify and install the plugin. |
| `db/install.xml` | Defines the `local_training_letter` table using Moodle XMLDB, so Moodle can create it on supported database engines. |
| `db/access.php` | Declares `submit`, `viewall`, and `approve` capabilities. Manager roles get the coordinator capabilities by default. |
| `classes/form/request_form.php` | Defines the student form with Moodle's standard fields, input types, and required rules. |
| `classes/form/review_form.php` | Defines the coordinator decision and note form. |
| `index.php` | Shows the portal home page and links available to the signed-in user. |
| `apply.php` | Requires login and submit capability, validates and saves a new request. |
| `myrequests.php` | Lists only the current user's requests and approved PDF links. |
| `manage.php` | Requires the view-all capability and lists requests for coordinators. |
| `review.php` | Requires approval capability, loads one request, and updates its status and note. |
| `getletter.php` | Checks ownership/coordination access and approved status before generating PDF output. |
| `lib.php` | Adds student and coordinator links to Moodle navigation when their capabilities allow it. |
| `lang/en/local_training_letter.php` | Holds labels and messages so interface text is not scattered through PHP files. |
| `styles.css` | Adds a responsive blue-and-white portal, cards, form sections, and status labels. |

## Install into Moodle

1. Unzip this project.
2. Copy the folder named `moodle-training-letter` into your Moodle plugin directory and rename it to `training_letter`.

   The final path should look like:

   ```text
   /path/to/moodle/public/local/training_letter/version.php
   /path/to/moodle/public/local/training_letter/index.php
   ```

   On a local XAMPP Windows installation, the path might look like:

   ```text
   C:\xampp\htdocs\moodle\public\local\training_letter\version.php
   ```

   Use the actual folder where your Moodle site is installed.

3. Sign in to Moodle as an administrator.
4. Open **Site administration → Notifications**. Moodle detects the new plugin, creates its table, and installs its capabilities.
5. Purge Moodle caches if the navigation link does not appear immediately: **Site administration → Development → Purge caches**.
6. Open the portal home page at:

   ```text
   https://your-moodle-site.example/local/training_letter/index.php
   ```

For Moodle 5.1 and newer, plugin code is inside the Moodle `public` directory. On older Moodle releases, use `/path/to/moodle/local/training_letter` instead. The plugin appears in Moodle navigation for users who can submit a request. Coordinators with the view-all capability also get an **All training requests** link.

## Configure coordinator access

By default, `db/access.php` grants `viewall` and `approve` to Moodle's Manager role archetype. To use another role, an administrator can grant these capabilities in Moodle:

1. Open **Site administration → Users → Permissions → Define roles**.
2. Edit the role intended for T&P coordinators.
3. Allow `local/training_letter:viewall` and `local/training_letter:approve`.
4. Assign that role to the coordinator at the **system context** (site-wide).

Students need `local/training_letter:submit`. The capability is allowed to authenticated users by default; administrators can adjust it to match their Moodle role setup. Moodle login is required for all plugin pages.

## Try a complete sample flow

1. Sign in with a student account and open **Training Letter Portal**.
2. Choose **Apply now**.
3. Fill in the company name and address, branch, semester, training details, duration, and proposed start date. Submit the form.
4. The request appears in **My requests** with status **Pending**. A student cannot download it yet.
5. Sign in with a coordinator account that has the capabilities above. Open **Coordinator workspace**, then choose **Review** on the sample request.
6. Select **Approved**, optionally enter a note, and save the decision.
7. Return to the student account. The approved request now has **Preview letter PDF** and **Download request letter PDF** links.
8. Save the downloaded PDF as the sample exported letter requested by the task.

The generated PDF uses a clean text header and a blank coordinator signature line. The project does not include an approved institutional logo, exact official letterhead artwork, postal address, or a real signature/seal. Replace the sample header and wording with assets and text approved by the institution before using it as an official outgoing letter.
### Student portal
![Student page](screenshots/student%20page.png)

### Coordinator dashboard
![Coordinator page](screenshots/coordinator%20page.png)
## How the form and database work

### Student form

The class in `classes/form/request_form.php` extends Moodle's `moodleform`. It declares each field, sets a Moodle parameter type, and adds required-field rules that run in the browser and on the server. Moodle handles the form submission and session key, then `apply.php` processes the returned data.
### Training request form
![Training form](screenshots/training%20form.png)

### Saving a request

The core save operation in `apply.php` is:

```php
$record = (object) [
    'userid' => $USER->id,
    'studentname' => $data->studentname,
    'rollno' => $data->rollno,
    'branch' => $data->branch,
    'semester' => $data->semester,
    'organization' => $data->organization,
    'postaladdress' => $data->postaladdress,
    'trainingtype' => $data->trainingtype,
    'trainingtitle' => $data->trainingtitle,
    'duration' => $data->duration,
    'startdate' => $data->startdate,
    'status' => 'pending',
    'coordinatornote' => null,
    'timecreated' => time(),
    'timemodified' => time(),
];

$DB->insert_record('local_training_letter', $record);
```

`$DB` is Moodle's database abstraction. It uses the database connection already configured for Moodle. `insert_record()` writes a record to the plugin table and avoids hard-coding database-specific SQL in the page.

### Request status

The `status` field begins as `pending`. The coordinator can change it to `approved` or `rejected`. `review.php` saves the new status, note, and modified time with `$DB->update_record()`.
### My requests
![My request](screenshots/My%20request.png)

The download page checks the status again on the server. Hiding the download link in the student page is not enough by itself; someone could try to open the URL directly. The server therefore refuses PDF generation unless the request is approved and the current user owns the request or has coordinator approval capability.

## PDF generation

`getletter.php` loads Moodle's `lib/pdflib.php` and uses Moodle's bundled `\pdf` class (TCPDF-based). It inserts escaped values from the request into a simple letter, then streams it to the browser:

```php
$pdf = new \pdf();
$pdf->SetMargins(22, 25, 22);
$pdf->AddPage();
$pdf->SetFont('freesans', '', 12);
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output($filename, $outputmode);
```

`action=preview` uses inline PDF output, while `action=download` downloads the file. The data is passed through Moodle's `s()` escaping helper before it is placed into the PDF HTML.

### Generated letter preview
![Preview letter](screenshots/preview%20letter.png)


## Security and Moodle conventions

- Each page calls `require_login()`.
- Pages check the relevant capability with `require_capability()`.
- Students see only rows filtered by their own Moodle `userid`.
- Coordinator pages check coordinator-specific capabilities.
- Form submissions use Moodle's Form API, browser/server required checks, and session key handling.
- Request IDs and action parameters are read using Moodle's parameter functions.
- User text is escaped with `s()` before HTML/PDF output.
- PDF authorization is checked again on the server and requires an approved request.
- No database password or independent DB connection is stored in this plugin.

## Troubleshooting

| Problem | What to check |
| --- | --- |
| Moodle does not detect the plugin | Confirm the folder is named `training_letter` and is directly inside Moodle's `local` folder. Check that `version.php` is at the plugin root. |
| Table is missing | Visit **Site administration → Notifications** while logged in as administrator and complete the plugin install/upgrade. |
| Student cannot open the form | Confirm the user is logged in and has `local/training_letter:submit`. |
| Coordinator cannot see the management page | Grant `local/training_letter:viewall` and `local/training_letter:approve` to the coordinator role at system context. |
| PDF link is not shown | The request must be approved first. |
| PDF opens as an error | Check Moodle's PHP error log and confirm the Moodle installation includes/enables its standard PDF library. |
| Navigation link is missing | Purge Moodle caches, then confirm the logged-in user has the matching capability. |

## Interview explanation

### 30-second overview

> “I built a Moodle local plugin for training request letters. Students submit their training and company details using Moodle's Form API. The plugin saves each request in a Moodle database table using the `$DB` API and starts it in Pending status. A coordinator with the right Moodle capability can review and approve or reject it. After approval, the student can preview or download a PDF generated with Moodle's bundled PDF library. I used Moodle capabilities and server-side checks so users cannot access coordinator pages or download an unapproved letter just by guessing a URL.”

### Common questions

**Why is this a local plugin?**  
The task describes a site-level Moodle extension under `local/training_letter`, rather than a course activity. Moodle's local plugin type fits custom site functionality that does not need to be a separate activity type.

**What does `version.php` do?**  
It identifies the plugin component and its version, and declares the minimum Moodle version. Moodle checks it during installation and upgrades.

**What does `install.xml` do?**  
It describes the plugin database table in Moodle's XMLDB format. Moodle can translate that portable definition to the database engine configured by the site.

**Why use `$DB` instead of writing SQL?**  
`$DB` is Moodle's database layer. It uses Moodle's configured connection and provides database-independent operations such as `insert_record`, `get_records`, and `update_record`.

**How does approval work?**  
New requests are stored as Pending. A coordinator with the approve capability updates the status. The PDF endpoint checks for Approved status before returning a letter.

**What is a Moodle capability?**  
A capability is a permission that Moodle assigns to roles in a context. This plugin defines submit, view-all, and approve permissions in `db/access.php` and checks them in the page scripts.

**Does this create a separate API endpoint?**  
No. It is a server-rendered Moodle plugin using Moodle forms and page scripts. It uses Moodle's PHP APIs and `$DB`, rather than building a standalone REST API.

**Is this the institution's final official letterhead?**  
No. This sample creates an approval-gated PDF with a basic header and signature placeholder. The institution should provide and approve the actual letterhead, exact wording, and signature process before using it as an official document.

**What could be improved next?**  
Add an approval history/audit trail, configurable institution details, a reviewed official letterhead, automated coordinator notifications, and automated Moodle plugin tests.

## Official Moodle references

- [Local plugins](https://moodledev.io/docs/5.0/apis/plugintypes/local)
- [Moodle Forms API](https://moodledev.io/docs/5.3/apis/subsystems/form)
- [Moodle Data manipulation API](https://moodledev.io/docs/4.5/apis/core/dml)
- [Moodle Access API](https://moodledev.io/docs/4.5/apis/subsystems/access)
- [XMLDB editor and database definitions](https://moodledev.io/general/development/tools/xmldb)
