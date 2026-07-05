<?php

namespace App\Livewire;

use App\Models\Lead;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';
    public string $whatsapp = '';
    public string $company = '';
    public string $message = '';
    
    public bool $submitted = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:100',
            'whatsapp' => 'required|string|min:10|max:20',
            'company' => 'nullable|string|max:100',
            'message' => 'nullable|string|max:1000',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'name.min' => 'Nama minimal 2 karakter',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi',
            'whatsapp.min' => 'Nomor WhatsApp tidak valid',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        // Create lead in CRM
        Lead::create([
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'company' => $this->company ?: null,
            'status' => 'new',
            'source' => 'web_contact',
            'notes' => $this->message ?: 'Lead dari halaman Contact',
        ]);

        // Show success message
        $this->submitted = true;
        
        // Reset form
        $this->reset(['name', 'whatsapp', 'company', 'message']);
    }

    public function resetForm(): void
    {
        $this->submitted = false;
        $this->reset(['name', 'whatsapp', 'company', 'message']);
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
