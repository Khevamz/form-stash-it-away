
import React, { useState } from 'react';
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";

const mockSubmissions = [
  { 
    id: 1, 
    formId: 'contact', 
    date: '2025-04-16 10:30:45',
    data: {
      name: 'John Doe',
      email: 'john@example.com',
      message: 'This is a test message.'
    }
  },
  { 
    id: 2, 
    formId: 'contact', 
    date: '2025-04-15 14:22:10',
    data: {
      name: 'Jane Smith',
      email: 'jane@example.com',
      message: 'Please contact me regarding your services.'
    }
  },
  { 
    id: 3, 
    formId: 'newsletter', 
    date: '2025-04-14 09:15:33',
    data: {
      email: 'alice@example.com',
      subscribe: true
    }
  }
];

const FormSubmissions = () => {
  const [selectedForm, setSelectedForm] = useState<string>('all');
  const [searchTerm, setSearchTerm] = useState<string>('');
  const [submissions, setSubmissions] = useState(mockSubmissions);
  const [viewingSubmission, setViewingSubmission] = useState<any>(null);

  // Filter submissions based on selected form and search term
  const filteredSubmissions = submissions.filter(submission => {
    const matchesForm = selectedForm === 'all' || submission.formId === selectedForm;
    const matchesSearch = searchTerm === '' || 
      JSON.stringify(submission.data).toLowerCase().includes(searchTerm.toLowerCase());
    
    return matchesForm && matchesSearch;
  });

  const formList = ['all', ...new Set(submissions.map(sub => sub.formId))];

  const handleDeleteSubmission = (id: number) => {
    setSubmissions(submissions.filter(sub => sub.id !== id));
    if (viewingSubmission && viewingSubmission.id === id) {
      setViewingSubmission(null);
    }
  };

  const handleViewSubmission = (submission: any) => {
    setViewingSubmission(submission);
  };

  const handleCloseView = () => {
    setViewingSubmission(null);
  };

  const handleExport = () => {
    // In a real plugin, this would export to CSV/Excel
    const dataStr = JSON.stringify(filteredSubmissions, null, 2);
    const dataUri = 'data:application/json;charset=utf-8,' + encodeURIComponent(dataStr);
    
    const downloadAnchorNode = document.createElement('a');
    downloadAnchorNode.setAttribute('href', dataUri);
    downloadAnchorNode.setAttribute('download', 'form_submissions.json');
    document.body.appendChild(downloadAnchorNode);
    downloadAnchorNode.click();
    downloadAnchorNode.remove();
  };

  return (
    <div className="max-w-6xl mx-auto p-6">
      <h2 className="text-2xl font-bold mb-6">Form Submissions</h2>
      
      <Card className="p-6">
        <div className="flex flex-col md:flex-row gap-4 mb-6">
          <div className="flex-1">
            <Select value={selectedForm} onValueChange={setSelectedForm}>
              <SelectTrigger className="w-full">
                <SelectValue placeholder="Select form" />
              </SelectTrigger>
              <SelectContent>
                {formList.map((form) => (
                  <SelectItem key={form} value={form}>
                    {form === 'all' ? 'All Forms' : form}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>
          
          <div className="flex-1">
            <Input
              placeholder="Search submissions..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
            />
          </div>
          
          <div>
            <Button onClick={handleExport}>Export</Button>
          </div>
        </div>
        
        {filteredSubmissions.length > 0 ? (
          <div className="border rounded-md">
            <Table>
              <TableCaption>A list of form submissions</TableCaption>
              <TableHeader>
                <TableRow>
                  <TableHead>ID</TableHead>
                  <TableHead>Form</TableHead>
                  <TableHead>Date</TableHead>
                  <TableHead>Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {filteredSubmissions.map((submission) => (
                  <TableRow key={submission.id}>
                    <TableCell>{submission.id}</TableCell>
                    <TableCell>{submission.formId}</TableCell>
                    <TableCell>{submission.date}</TableCell>
                    <TableCell>
                      <div className="flex space-x-2">
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => handleViewSubmission(submission)}
                        >
                          View
                        </Button>
                        <Button
                          variant="destructive"
                          size="sm"
                          onClick={() => handleDeleteSubmission(submission.id)}
                        >
                          Delete
                        </Button>
                      </div>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        ) : (
          <div className="text-center py-10 text-gray-500">
            No submissions found.
          </div>
        )}
      </Card>
      
      {viewingSubmission && (
        <div className="mt-8">
          <Card className="p-6">
            <div className="flex justify-between items-center mb-4">
              <h3 className="text-xl font-semibold">
                Submission Details (ID: {viewingSubmission.id})
              </h3>
              <Button variant="outline" onClick={handleCloseView}>Close</Button>
            </div>
            
            <div className="grid grid-cols-1 gap-4">
              <div>
                <p className="text-sm font-medium text-gray-500">Form</p>
                <p>{viewingSubmission.formId}</p>
              </div>
              <div>
                <p className="text-sm font-medium text-gray-500">Date</p>
                <p>{viewingSubmission.date}</p>
              </div>
              
              <div className="border-t pt-4">
                <p className="text-sm font-medium text-gray-500 mb-2">Form Data</p>
                <div className="bg-gray-50 p-4 rounded-md">
                  {Object.entries(viewingSubmission.data).map(([key, value]: [string, any]) => (
                    <div key={key} className="mb-2">
                      <p className="text-sm font-medium">{key}:</p>
                      <p className="ml-4">{typeof value === 'boolean' ? 
                        (value ? 'Yes' : 'No') : 
                        String(value)}</p>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          </Card>
        </div>
      )}
    </div>
  );
};

export default FormSubmissions;
