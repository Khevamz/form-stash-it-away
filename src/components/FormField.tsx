
import React from 'react';
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import type { Field } from './FormBuilder';

interface FormFieldProps {
  field: Field;
}

export const FormField: React.FC<FormFieldProps> = ({ field }) => {
  const renderField = () => {
    switch (field.type) {
      case 'text':
        return (
          <Input 
            id={field.id} 
            placeholder={field.placeholder} 
            required={field.required} 
          />
        );
      case 'email':
        return (
          <Input 
            id={field.id} 
            placeholder={field.placeholder} 
            required={field.required} 
            type="email" 
          />
        );
      case 'textarea':
        return (
          <Textarea 
            id={field.id} 
            placeholder={field.placeholder} 
            required={field.required} 
          />
        );
      case 'select':
        return (
          <Select>
            <SelectTrigger>
              <SelectValue placeholder={field.placeholder || 'Select an option'} />
            </SelectTrigger>
            <SelectContent>
              {field.options?.map((option, index) => (
                <SelectItem key={index} value={option}>
                  {option}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        );
      case 'checkbox':
        return (
          <div className="flex items-center space-x-2">
            <Checkbox id={field.id} required={field.required} />
            <Label htmlFor={field.id}>{field.placeholder || field.label}</Label>
          </div>
        );
      default:
        return <Input id={field.id} />;
    }
  };

  return (
    <div className="space-y-2">
      {field.type !== 'checkbox' && (
        <div className="flex items-center justify-between">
          <Label htmlFor={field.id}>
            {field.label}
            {field.required && <span className="text-red-500 ml-1">*</span>}
          </Label>
        </div>
      )}
      {renderField()}
    </div>
  );
};
