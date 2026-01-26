# ITEKES Bali Journal

This is a journal system that used to do a research review request, community service review request, and request for an ethical clearance document.

## Base Workflow

### As Research Submitter

[SUBMITTER] Create a proposal
[SYSTEM] SET STATUS=NEED_REVIEW STAGE=PROPOSAL

[REVIEWER] Check it
[REVIEWER IF APPROVE][SYSTEM] SET STATUS=REVISION_NEEDED STAGE=PROGRESS-REPORT
[REVIEWER IF REJECT][SYSTEM] SET STATUS=REJECTED STAGE=PROPOSAL
[REVIEWER IF REVISION_NEEDED][SYSTEM] SET STATUS=REVISION_NEEDED STAGE=PROPOSAL

...repeatedly until...

STATUS=APPROVED STAGE=FINAL-REPORT

### As Community Service Submitter

[SUBMITTER] Create a proposal
[SYSTEM] SET STATUS=NEED_REVIEW STAGE=PROPOSAL

[REVIEWER] Check it
[REVIEWER IF APPROVE][SYSTEM] SET STATUS=REVISION_NEEDED STAGE=PROGRESS-REPORT
[REVIEWER IF REJECT][SYSTEM] SET STATUS=REJECTED STAGE=PROPOSAL
[REVIEWER IF REVISION_NEEDED][SYSTEM] SET STATUS=REVISION_NEEDED STAGE=PROPOSAL

...repeatedly until...

STATUS=APPROVED STAGE=FINAL-REPORT

### Form Fields

Every form fields is strictly and should exact the stage needs. You don't allowed to change or modify it. I already add a comment on submission detail fillable that explain the stage needs.

## Roles & Permissions

This system has several reserved role, such as:

- admin
- lecture
- guest
- operator
- reviewer-research
- reviewer-community-service

This system also has several immutable permissions:

- manage users
- manage base
- manage form
- request research review
- request community service review
- request ethics review
- assign reviewer research
- assign reviewer community service
- review research
- review community service

## Tech Stack

In this project we will provide stable first approach that prioritize the development has a stable solution instead of too fancy. Also, a clean and robust code needed here in order to produce a scalable and maintanable code in the future.

### Backend

On the backend this system using:

- Laravel 12 (the base)
- InertiaJS (the glue for using modern stack on frontend)
- spatie/laravel-permission (the permission management)
- Laravel Sail

Because this system using Laravel Sail, you must use `sail` command to run any artisan command. For example `sail artisan migrate`, `sail npm ...`, `sail npx ...`, .

#### Inertia Props Share

I already created the InertiaJS props share on the `app/Http/Middleware/HandleInertiaRequest.php`. That middleware send the current user and their roles and permissions to the frontend, and for now it mainly used on the frontend sidebar.

#### The Controller

In the current phase, we still store the logic on the controller. But in the future we will develop the better solution. So, you can put the logic on the controller for now.

#### Route Files

There are some modules of route files that separated by its functionality.

- `routes/web.php`: The main route file that handle the inertia page rendering.
- `routes/modules/general.php`: The route file that handle the general functionality.
- `routes/modules/review_request.php`: The route file that handle the review request functionality.
- `routes/modules/reviewer.php`: The route file that handle the reviewer functionality.
- `routes/modules/reviewer_assignment.php`: The route file for assign the reviewer of an submission.
- `routes/modules/base_system.php`: The route for handling base data of this system. It sometimes just a simple CRUD.
- `routes/modules/settigs.php`: The user settings page.
- `routes/modules/users.php`: The users management route. It include role and permissions too.

#### File Upload

We use centralized file upload system. The route is on `/storage-upload` and the controller is on `app/Http/Controllers/General/StorageUploadController.php`. It requires two parameters: `action` used for identifier of what validation should applied and where the file should stored. And the second one is the `file` parameter. You can see the action list on `app/Enums/StorageUploadAction.php`.

> See the frontend file upload docs below (on frontend section).

### Frontend

On the frontend this system using InertiaJS + Svelte version 5, Shadcn Svelte, lucide-icon, TailwindCSS. Also, on the frontend you always should use Bahasa Indonesia. But some words that technically need to be English, you should use English.

#### Frontend UI/UX

You should always create a good UI/UX, not too fancy but has stable functionality. The design should be simple and clean with too many stacking cards. The responsiveness is the priority for the design. If you need to create a confirmation or modal, please use the shadcn provided component such as: `dialog`, `alert-dialog`.

#### File Upload

You always should use the file upload system on the backend. See the docs above (on backend section).

I already setup the file upload component on the frontend. You can see it on `resources/js/components/UploadFile.svelte`. That component will retrieve a prop named as "action". For the action list you check on `resources/js/data/storage-upload.ts`.

#### The term of frontend development

##### Directory Management

We have a strong convention about folder management on the frontend.

> The frontend root folder is on `resources/js`

The structure looks like this:

- pages (store the base page that used directly by the backend Inertia)
- layouts (store reusable layouts)
- components (store the reusable piece of component or section of the app)
- components/ui (the preserved location by the shadcn svelte)
- data/menu.ts (sidebar menu items)
- data/permission-and-role.ts (a centralized place to use const of role and permission that defined on the backend)
- types (store typing of the app item)

##### Svelte 5 Rules

All new components must strictly use Svelte 5 Runes syntax. Avoid legacy `export let` or `$:`. Use `$props()` for props. You should always use the best practice of svelte lifecycle in order to make the reliable code.

##### Every Page Development

In this application, we follow a strong convention when writing a page content (and its layout). You must be wrap it using `<LayoutComposer>` that can loaded from `layouts/LayoutComposer.svelte`.

```svelte
<script lang="ts">
    import AppLayout from '@/layouts/AppLayout.svelte';
    import LayoutComposer from '@/layouts/LayoutComposer.svelte';
    import Heading from '@/components/Heading.svelte';
    import { type BreadcrumbItem } from '@/types';

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Pengguna',
            href: '/users',
        },
    ];
</script>

<svelte:head>
    <title>TITLE HERE</title>
</svelte:head>

<AppLayout {breadcrumbs}>
    <LayoutComposer>
        {#snippet header()}
            <!-- heading. Please use standard heading on components/Heading.svelte -->
            <Heading title="">
        {/snippet}

        {#snippet actions()}
            <!-- button like "create" button  -->
        {/snippet}

        {#snippet filters()}
            <!-- It should be a group of filters like search, dropdown -->
        {/snippet}

        {#snippet backButton()}
            <!-- Sometime when we have detail page, we need a back button to go back to the list page. -->
        {/snippet}

        {#snippet children()}
            <!-- The place where main content showed up, like table or something -->
        {/snippet}
    </LayoutComposer>
</AppLayout>
```

##### Form Submission on Inertia

Use Inertia's useForm for state management and submission handling. We always rely on stable rules of what tech stack we use.

##### Data Filter

Filters should update the URL query parameters and trigger a server-side reload via Inertia router.

##### Shadcn Svelte Convention

You should use shadcn-svelte idiomatic like on the official documentation. The shadcn preserved directory is on `components/ui/...`. You can install a new shadcn component by using `sail shadcn-svelte@latest add ...[component-name]`. You're permitted to check the documentation of the component you want to use by using this URL `https://www.shadcn-svelte.com/docs/components/[component-name]` (e.g https://www.shadcn-svelte.com/docs/components/select).

Always use the new `field` component instead of traditional form when you want to make a form. Why? because the developer now already stop the `form` component and start on the `field` component.

Prefer to use `native-select` component over `select` component. Because it more lightweight and user friendly.

### Database

On the database this system using MySQL. You should always use the best practice of database development. So we can have the best performance for this application.
