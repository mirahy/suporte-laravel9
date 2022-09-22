import { TestBed } from '@angular/core/testing';

import { FaculdadeService } from './faculdade.service';

describe('FaculdadeService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: FaculdadeService = TestBed.get(FaculdadeService);
    expect(service).toBeTruthy();
  });
});
