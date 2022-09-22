import { TestBed } from '@angular/core/testing';

import { SuperMacroService } from './super-macro.service';

describe('SuperMacroService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: SuperMacroService = TestBed.get(SuperMacroService);
    expect(service).toBeTruthy();
  });
});
