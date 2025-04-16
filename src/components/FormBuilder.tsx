
import React, { useState, useEffect } from 'react';
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Switch } from "@/components/ui/switch";
import { Label } from "@/components/ui/label";
import { toast } from "sonner";
import FormPreview from './FormPreview';
import { FormField } from './FormField';

export interface Field {
  id: string;
  type: 'text' | 'email' | 'textarea' | 'select' | 'checkbox';
  label: string;
  placeholder?: string;
  required: boolean;
  options?: string[];
}

const FormBuilder = () => {
  const [formName, setFormName] = useState('');
  const [fields, setFields] = useState<Field[]>([]);
  const [currentField, setCurrentField] = useState<Field>({
    id: '',
    type: 'text',
    label: '',
    placeholder: '',
    required: false,
    options: []
  });
  const [showOptions, setShowOptions] = useState(false);
  const [option, setOption] = useState('');
  const [isEditing, setIsEditing] = useState(false);
  const [editIndex, setEditIndex] = useState(-1);
  const [successMessage, setSuccessMessage] = useState('Thank you for your submission!');

  useEffect(() => {
    setShowOptions(currentField.type === 'select');
  }, [currentField.type]);

  const generateId = () => {
    return Math.random().toString(36).substring(2, 9);
  };

  const handleAddOption = () => {
    if (option.trim() !== '') {
      setCurrentField({
        ...currentField,
        options: [...(currentField.options || []), option.trim()]
      });
      setOption('');
    }
  };

  const handleRemoveOption = (index: number) => {
    const updatedOptions = [...(currentField.options || [])];
    updatedOptions.splice(index, 1);
    setCurrentField({
      ...currentField,
      options: updatedOptions
    });
  };

  const handleAddField = () => {
    if (currentField.label.trim() === '') {
      toast.error('Field label cannot be empty');
      return;
    }

    if (currentField.type === 'select' && (!currentField.options || currentField.options.length === 0)) {
      toast.error('Select field must have at least one option');
      return;
    }

    const newField = {
      ...currentField,
      id: generateId()
    };

    if (isEditing) {
      const updatedFields = [...fields];
      updatedFields[editIndex] = newField;
      setFields(updatedFields);
      setIsEditing(false);
      setEditIndex(-1);
    } else {
      setFields([...fields, newField]);
    }

    // Reset form
    setCurrentField({
      id: '',
      type: 'text',
      label: '',
      placeholder: '',
      required: false,
      options: []
    });
  };

  const handleEditField = (index: number) => {
    setCurrentField(fields[index]);
    setIsEditing(true);
    setEditIndex(index);
    if (fields[index].type === 'select') {
      setShowOptions(true);
    }
  };

  const handleDeleteField = (index: number) => {
    const updatedFields = [...fields];
    updatedFields.splice(index, 1);
    setFields(updatedFields);
  };

  const handleSaveForm = () => {
    if (formName.trim() === '') {
      toast.error('Form name cannot be empty');
      return;
    }

    if (fields.length === 0) {
      toast.error('Form must have at least one field');
      return;
    }

    // In a real WordPress plugin, this would save to the database
    const formData = {
      name: formName,
      fields: fields,
      successMessage: successMessage
    };

    console.log('Form saved:', formData);
    toast.success('Form saved successfully!');
  };

  const cancelEdit = () => {
    setCurrentField({
      id: '',
      type: 'text',
      label: '',
      placeholder: '',
      required: false,
      options: []
    });
    setIsEditing(false);
    setEditIndex(-1);
  };

  return (
    <div className="max-w-4xl mx-auto p-6">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
          <h2 className="text-2xl font-bold mb-6">Form Builder</h2>
          <Card className="p-6">
            <div className="mb-6">
              <Label htmlFor="form-name">Form Name</Label>
              <Input 
                id="form-name" 
                value={formName} 
                onChange={(e) => setFormName(e.target.value)} 
                placeholder="Enter form name"
                className="mt-2"
              />
            </div>

            <div className="mb-6">
              <Label htmlFor="success-message">Success Message</Label>
              <Input 
                id="success-message" 
                value={successMessage} 
                onChange={(e) => setSuccessMessage(e.target.value)} 
                placeholder="Message to display after submission"
                className="mt-2"
              />
            </div>

            <div className="space-y-6 border-t pt-6">
              <h3 className="text-lg font-semibold">
                {isEditing ? 'Edit Field' : 'Add New Field'}
              </h3>
              
              <div className="space-y-4">
                <div>
                  <Label htmlFor="field-type">Field Type</Label>
                  <Select 
                    value={currentField.type}
                    onValueChange={(value: 'text' | 'email' | 'textarea' | 'select' | 'checkbox') => {
                      setCurrentField({...currentField, type: value});
                      setShowOptions(value === 'select');
                    }}
                  >
                    <SelectTrigger className="mt-2">
                      <SelectValue placeholder="Select field type" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="text">Text</SelectItem>
                      <SelectItem value="email">Email</SelectItem>
                      <SelectItem value="textarea">Textarea</SelectItem>
                      <SelectItem value="select">Dropdown</SelectItem>
                      <SelectItem value="checkbox">Checkbox</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                
                <div>
                  <Label htmlFor="field-label">Label</Label>
                  <Input 
                    id="field-label" 
                    value={currentField.label} 
                    onChange={(e) => setCurrentField({...currentField, label: e.target.value})}
                    placeholder="Enter field label"
                    className="mt-2"
                  />
                </div>
                
                {currentField.type !== 'checkbox' && (
                  <div>
                    <Label htmlFor="field-placeholder">Placeholder (optional)</Label>
                    <Input 
                      id="field-placeholder" 
                      value={currentField.placeholder || ''} 
                      onChange={(e) => setCurrentField({...currentField, placeholder: e.target.value})}
                      placeholder="Enter placeholder text"
                      className="mt-2"
                    />
                  </div>
                )}
                
                <div className="flex items-center space-x-2">
                  <Switch 
                    id="field-required"
                    checked={currentField.required}
                    onCheckedChange={(checked) => setCurrentField({...currentField, required: checked})}
                  />
                  <Label htmlFor="field-required">Required field</Label>
                </div>
                
                {showOptions && (
                  <div className="space-y-3 border-t pt-3">
                    <Label>Options</Label>
                    <div className="flex space-x-2">
                      <Input 
                        value={option} 
                        onChange={(e) => setOption(e.target.value)}
                        placeholder="Enter option"
                        className="flex-1"
                      />
                      <Button onClick={handleAddOption}>Add</Button>
                    </div>
                    
                    {currentField.options && currentField.options.length > 0 && (
                      <div className="mt-3">
                        <Label>Current Options:</Label>
                        <ul className="mt-2 space-y-2">
                          {currentField.options.map((opt, index) => (
                            <li key={index} className="flex items-center justify-between bg-gray-100 p-2 rounded">
                              <span>{opt}</span>
                              <Button 
                                variant="destructive" 
                                size="sm" 
                                onClick={() => handleRemoveOption(index)}
                              >
                                Remove
                              </Button>
                            </li>
                          ))}
                        </ul>
                      </div>
                    )}
                  </div>
                )}
                
                <div className="flex space-x-2 pt-3">
                  <Button onClick={handleAddField}>
                    {isEditing ? 'Update Field' : 'Add Field'}
                  </Button>
                  {isEditing && (
                    <Button variant="outline" onClick={cancelEdit}>
                      Cancel
                    </Button>
                  )}
                </div>
              </div>
            </div>
          </Card>

          <div className="mt-6">
            <Button onClick={handleSaveForm} className="w-full">Save Form</Button>
          </div>

          <div className="mt-6">
            <div className="bg-gray-100 p-4 rounded-md">
              <h3 className="text-lg font-semibold mb-2">Shortcode:</h3>
              <code className="bg-gray-200 p-2 rounded block">
                [form_stash id="your_form_id"]
              </code>
              <p className="text-sm text-gray-600 mt-2">
                Copy this shortcode and paste it into any post or page to display your form.
              </p>
            </div>
          </div>
        </div>

        <div>
          <h2 className="text-2xl font-bold mb-6">Form Preview</h2>
          <Card className="p-6">
            {formName && <h3 className="text-xl font-semibold mb-4">{formName}</h3>}
            
            {fields.length === 0 ? (
              <p className="text-gray-500 italic">Add fields to see a preview</p>
            ) : (
              <div className="space-y-4">
                <FormPreview fields={fields} />
                
                <Button className="w-full">Submit</Button>
              </div>
            )}
          </Card>

          {fields.length > 0 && (
            <div className="mt-8">
              <h3 className="text-xl font-semibold mb-4">Form Structure</h3>
              <div className="space-y-3">
                {fields.map((field, index) => (
                  <div key={field.id} className="bg-gray-50 p-4 rounded-lg border">
                    <div className="flex justify-between items-center mb-2">
                      <span className="font-medium">{field.label}</span>
                      <div className="space-x-2">
                        <Button 
                          variant="outline" 
                          size="sm" 
                          onClick={() => handleEditField(index)}
                        >
                          Edit
                        </Button>
                        <Button 
                          variant="destructive" 
                          size="sm" 
                          onClick={() => handleDeleteField(index)}
                        >
                          Delete
                        </Button>
                      </div>
                    </div>
                    <div className="text-sm text-gray-600">
                      <p>Type: {field.type}</p>
                      {field.placeholder && <p>Placeholder: {field.placeholder}</p>}
                      <p>Required: {field.required ? 'Yes' : 'No'}</p>
                      {field.options && field.options.length > 0 && (
                        <p>Options: {field.options.join(', ')}</p>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default FormBuilder;
