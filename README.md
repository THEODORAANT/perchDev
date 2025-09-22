# Perch

## AI Content Generation

Perch includes an optional “Generate with AI” assistant inside the control panel to help draft content
for both content regions and blog posts. The following sections explain how to configure and use it
safely.

### Prerequisites

* Sign in to the Perch admin area before attempting to generate AI content.
* Provide an OpenAI API key by either setting an `OPENAI_API_KEY` environment variable before
  launching Perch or by defining it in `perch/config/config.php`, for example:

  ```php
  define('OPENAI_API_KEY', 'sk-XXXX');
  ```

* Ensure the PHP cURL extension is enabled on the server so the control panel can communicate with
  the OpenAI API.

### Entry Points

* **Content regions** — The submit bar in
  `perch/core/apps/content/modes/edit.form.post.php` includes a “Generate with AI” button. When
  triggered, the browser posts the request to `/core/apps/content/ai/generate.php` and writes the AI
  response into the first `<textarea>` inside the `#content-edit` form wrapper.
* **Blog posts** — `perch/addons/apps/perch_blog/modes/edit.post.php` offers the same button while
  editing posts. It calls `/addons/apps/perch_blog/ai/generate.php` and then fills the `<textarea>`
  contained within `#blog-edit`.

### Interactive Flow

Clicking “Generate with AI” opens a prompt dialog where you describe the content you would like the
model to produce. After the request is submitted, Perch retrieves up to 150 tokens of generated text
and inserts it into the relevant editor field. Always review and edit the suggestion before
publishing to ensure it matches your tone, house style, and legal obligations.
=======
# perch
Replaced the legacy ereg fallback with PCRE-based validation and simplified safe_stripslashes() in both PerchUtil libraries to drop deprecated magic quotes logic.

Removed runtime magic quotes handling from the MySQL helper and PHPMailer in both code paths so no deprecated functions are invoked during database quoting or file encoding.

Modernized Perch Shop helpers by using array_walk_recursive() for flattening and a closure in place of create_function() for delimiter conversion callbacks.
