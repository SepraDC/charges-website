<template>
    <main class="container mx-auto">
        <TabGroup>
            <TabList>
                <Tab>Tab 1</Tab>
                <Tab>Tab 2</Tab>
            </TabList>
            <TabPanels>
                <TabPanel>Content 1</TabPanel>
                <TabPanel>Content 2</TabPanel>
                <TabPanel>Content 3</TabPanel>
            </TabPanels>
        </TabGroup>
        <h1 class="text-red-700 my-2">Test first page</h1>
        <nav>
            <ul>
                <li><NuxtLink to="/charges" class="bg-blue-500 text-white rounded-lg p-2 hover:bg-blue-700">Prélevements</NuxtLink></li>
            </ul>
        </nav>
    </main>
</template>
<script lang="ts" setup>
import { TabGroup, TabList, Tab, TabPanels, TabPanel} from "@headlessui/vue"
</script>