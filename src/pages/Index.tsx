
import React, { useState } from 'react';
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import FormBuilder from '@/components/FormBuilder';
import FormSubmissions from '@/components/FormSubmissions';

const Index = () => {
  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <h1 className="text-3xl font-bold text-gray-900">Form Stash</h1>
          <p className="text-gray-600">Create forms and store submissions</p>
        </div>
      </header>
      
      <main className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <Tabs defaultValue="builder" className="w-full">
          <TabsList className="grid w-full max-w-md mx-auto mb-8 grid-cols-2">
            <TabsTrigger value="builder">Form Builder</TabsTrigger>
            <TabsTrigger value="submissions">Submissions</TabsTrigger>
          </TabsList>
          
          <TabsContent value="builder">
            <FormBuilder />
          </TabsContent>
          
          <TabsContent value="submissions">
            <FormSubmissions />
          </TabsContent>
        </Tabs>
      </main>
      
      <footer className="bg-white border-t mt-12">
        <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-gray-500">
          <p>Form Stash WordPress Plugin - Store form submissions easily</p>
          <p className="text-sm mt-2">© 2025 Form Stash</p>
        </div>
      </footer>
    </div>
  );
};

export default Index;
