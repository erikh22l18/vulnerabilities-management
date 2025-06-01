# Excel Vulnerability Import Guide

## Purpose

This guide explains how to use the Excel import feature to bulk-add or update vulnerabilities in the system. This feature is designed to streamline the process of populating vulnerability data, especially when dealing with multiple entries from external sources like scanning tools or spreadsheets.

## Template

To ensure successful import, data must be formatted according to a specific Excel template.

*   **Download Template:** You can download the official import template from the "Cargar Vulnerabilidades" page within the application, typically found at the `/vulnerabilities/charge` route.
*   **Columns:** The Excel file must contain the following columns in the specified order. Column names in the header row must match these exactly (case-insensitive during validation, but exact match is recommended).

    | Column Name          | Description                                                                                                | Formatting Notes                                                                                                |
    |----------------------|------------------------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------------------------------------------|
    | `título`             | The title or name of the vulnerability. (Required)                                                         | Text, max 255 characters.                                                                                       |
    | `fecha detección`    | The date when the vulnerability was detected.                                                              | YYYY-MM-DD format (e.g., 2023-10-27). Can also handle Excel numeric date format.                                 |
    | `proyecto`           | The name of the project this vulnerability belongs to. (Required)                                          | Must exactly match the name of an existing project in the system.                                               |
    | `descripción`        | A detailed description of the vulnerability.                                                               | Text.                                                                                                           |
    | `módulo afectado`    | The specific component, module, asset, or URL affected by the vulnerability.                               | Text, max 255 characters.                                                                                       |
    | `tipo`               | The type or classification of the vulnerability (e.g., SQL Injection, XSS).                                | Must exactly match the name of an existing vulnerability type in the system.                                    |
    | `estado`             | The current state of the vulnerability.                                                                    | Valid states: `Detectada`, `En tratamiento`, `Resuelta`, `Cerrada`.                                             |
    | `owasp`              | OWASP Top 10 classification (e.g., A01:2021-Broken Access Control).                                         | Text, max 255 characters.                                                                                       |
    | `vector cvss`        | The CVSS vector string (e.g., CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H).                                | Text, max 255 characters.                                                                                       |
    | `puntaje cvss`       | The CVSS score.                                                                                            | Numeric, typically 0.0 to 10.0.                                                                                 |
    | `severidad`          | The severity level of the vulnerability.                                                                   | E.g., `Baja`, `Media`, `Alta`, `Crítica`. (Ensure these match system's predefined values if any).                |
    | `probabilidad`       | The likelihood of the vulnerability being exploited.                                                       | Text or numeric, max 255 characters.                                                                            |
    | `impacto`            | The estimated impact if the vulnerability is exploited.                                                    | Text, max 255 characters.                                                                                       |
    | `responsable`        | The email address of the user responsible for addressing the vulnerability.                                | Must be a valid email of an existing user in the system.                                                        |
    | `fecha límite`       | The deadline for resolving the vulnerability.                                                              | YYYY-MM-DD format. Can also handle Excel numeric date format.                                                   |
    | `prioridad`          | The priority for addressing the vulnerability.                                                             | E.g., `Baja`, `Media`, `Alta`, `Crítica`. (Ensure these match system's predefined values if any).                |
    | `fuente`             | The source from which the vulnerability was identified (e.g., Manual Test, Nessus Scan, User Report).      | Text, max 255 characters.                                                                                       |
    | `url documentación`  | A URL linking to external documentation or more information about the vulnerability.                       | Valid URL format, max 255 characters.                                                                           |
    | `observaciones`      | Any additional notes or observations. These will be added as an initial comment to the vulnerability.      | Text.                                                                                                           |

    *Note on 'Asignada' state:* While the core model primarily uses 'Detectada', 'En tratamiento', 'Resuelta', 'Cerrada', the import validation might still accept 'Asignada' if it's present in the validation arrays used by `VulnerabilityController` or `VulnerabilityImport` class. If 'Asignada' is used in the Excel, it will be processed if the backend validation allows it.

## Process

1.  **Navigate:** Go to the "Cargar Vulnerabilidades" (Upload Vulnerabilities) page in the application. This is typically found at the route `/vulnerabilities/charge`.
2.  **Download Template:** If you don't have it, download the latest Excel template provided on the page.
3.  **Fill Template:** Carefully fill in the vulnerability details into the template, adhering to the column specifications and formatting notes.
4.  **Upload File:** Use the form on the "Cargar Vulnerabilidades" page to upload your completed Excel file.
5.  **Validation:**
    *   **Header Validation:** The system first checks if the uploaded file's header row matches the expected column names. If not, an error message will be displayed indicating missing or incorrect columns.
    *   **Row Data Validation:** If the headers are correct, the system then validates each data row for correctness (e.g., valid project names, user emails, states, date formats). A summary of errors found (if any) will be displayed, indicating which rows and fields have issues.
6.  **Processing:** If all validations pass, the file is queued for background processing. This means you can continue using the application while the import happens in the background.
7.  **Results:** Vulnerabilities are either created (if new) or updated (if existing, based on a combination of Title, Component, and Project). Any observations from the Excel sheet are added as comments to the respective vulnerabilities. You will receive a notification upon completion, or you can check the vulnerability list to see the imported/updated items.

## Duplicate Handling

The system identifies existing vulnerabilities based on a unique combination of:
*   **Título (Title)**
*   **Módulo afectado (Component)**
*   **Proyecto (Project)**

If a row in your Excel file matches an existing vulnerability on these three fields, the system will **update** the existing vulnerability record with the information from your Excel row. If no match is found, a **new** vulnerability record will be created.

## Important Notes

*   **Prerequisites:** Ensure that any Project names, User emails (for 'responsable'), and Vulnerability Types mentioned in your Excel file already exist in the system *before* starting the import. The import process typically does not create these related entities on the fly.
*   **Validation Errors:** Pay close attention to any error messages displayed after uploading the file. These messages will help you correct your Excel data for a successful import.
*   **Background Processing:** Since imports are handled in the background, there might be a short delay before all vulnerabilities appear in the system, especially for large files.
*   **Data Integrity:** While the system performs validations, it's crucial to ensure the accuracy and integrity of the data in your Excel file before uploading.
