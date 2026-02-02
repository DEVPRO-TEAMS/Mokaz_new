@extends('layouts.app')

@section('content')
    <div class="main-content-inne pt-5 mt-5 wrap-dashboard-content">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="fas fa-map me-2 text-danger"></i> Gestion des images des emplacements
                    </h3>
                </div>
            </div>
        </div>

        <div class="wrapper-content row">
            <div class="col-xl-12">
                <div class="card border-1 shadow-sm rounded-3 overflow-hidden">
                    <div class="card-body p-4">
                        <form action="{{ route('admin.storeLocationImage') }}"  method="POST" class="submitForm"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city_code" class="form-label">Ville</label>
                                    <select name="city_code" id="city_code" class="form-select" required>
                                        @foreach ($locations as $city)
                                            <option value="{{ $city->code }}">
                                                {{ $city->label }} ({{ $city->country?->label }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="image" class="form-label">Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" required>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="tf-btn primary">Enregistrer</button>
                            </div>
                        </form>

                        <hr>

                        <h4>Images actuelles</h4>
                        <div class="row">
                            @foreach ($images as $cityCode => $image)
                                <div class="col-md-3">
                                    <div class="card mb-3">
                                        <img src="{{ asset($image->image) }}" class="card-img-top" alt="Location image">
                                        <div class="card-body">
                                            <h6 class="card-title">{{ $image->city?->label }}
                                                ({{ $image->country?->label }})
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>



    </div>
@endsection
