
import React from 'react';
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import { FormField } from './FormField';
import type { Field } from './FormBuilder';

interface FormPreviewProps {
  fields: Field[];
}

const FormPreview: React.FC<FormPreviewProps> = ({ fields }) => {
  return (
    <div className="space-y-4">
      {fields.map((field) => (
        <div key={field.id} className="space-y-2">
          <FormField field={field} />
        </div>
      ))}
    </div>
  );
};

export default FormPreview;
