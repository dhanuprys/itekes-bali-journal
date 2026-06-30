<script lang="ts">
    import * as Resizable from '@/components/ui/resizable';
    let { details, actions } = $props();
</script>

<div class="h-[calc(100vh-12rem)] border rounded-lg bg-background shadow-sm overflow-hidden">
    <Resizable.PaneGroup direction="horizontal">
        <Resizable.Pane defaultSize={70}>
            <div class="h-full overflow-y-auto p-6 space-y-6">
                {@render details()}
            </div>
        </Resizable.Pane>

        <Resizable.Handle withHandle />

        <Resizable.Pane defaultSize={40} minSize={20}>
            <div class="h-full flex flex-col overflow-hidden bg-muted/10 border-l">
                {@render actions()}
            </div>
        </Resizable.Pane>
    </Resizable.PaneGroup>
</div>
